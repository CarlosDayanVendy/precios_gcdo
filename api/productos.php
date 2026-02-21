<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../config/db.php';
$pdo = getDb();

$method = $_SERVER['REQUEST_METHOD'];

// Obtener body en PUT/DELETE
$input = [];
if (in_array($method, ['POST', 'PUT'])) {
    $raw = file_get_contents('php://input');
    $input = json_decode($raw, true) ?? $_POST;
}

// Listar productos con precios
function listarProductos($pdo) {
    $sql = "SELECT p.id, p.codigo_sae, p.nombre_comercial, p.descripcion, p.unidad, p.activo,
                   lp.precio_publico, lp.precio_minimo, lp.precio_materialista, lp.precio_tiendas
            FROM productos p
            LEFT JOIN lista_precios lp ON lp.producto_id = p.id AND lp.activo = 1
            ORDER BY p.nombre_comercial";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

switch ($method) {
    case 'GET':
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            $sql = "SELECT p.id, p.codigo_sae, p.nombre_comercial, p.descripcion, p.unidad,
                           lp.precio_publico, lp.precio_minimo, lp.precio_materialista, lp.precio_tiendas
                    FROM productos p
                    LEFT JOIN lista_precios lp ON lp.producto_id = p.id AND lp.activo = 1
                    WHERE p.id = ? LIMIT 1";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) {
                http_response_code(404);
                echo json_encode(['error' => 'Producto no encontrado']);
            } else {
                echo json_encode($row);
            }
        } else {
            echo json_encode(listarProductos($pdo));
        }
        break;

    case 'POST':
        $codigo_sae = trim($input['codigo_sae'] ?? '');
        $nombre_comercial = trim($input['nombre_comercial'] ?? '');
        $descripcion = trim($input['descripcion'] ?? '');
        $unidad = trim($input['unidad'] ?? '');
        $precio_publico = (float)($input['precio_publico'] ?? 0);
        $precio_minimo = (float)($input['precio_minimo'] ?? 0);
        $precio_materialista = isset($input['precio_materialista']) ? (float)$input['precio_materialista'] : null;
        $precio_tiendas = isset($input['precio_tiendas']) ? (float)$input['precio_tiendas'] : null;

        if (empty($codigo_sae) || empty($nombre_comercial)) {
            http_response_code(400);
            echo json_encode(['error' => 'Código SAE y nombre comercial son obligatorios']);
            exit;
        }

        if ($precio_publico < 0 || $precio_minimo < 0 || $precio_materialista < 0 || $precio_tiendas < 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Todos los precios son obligatorios y no pueden ser negativos']);
            exit;
        }

        if (!isset($input['precio_materialista']) || !isset($input['precio_tiendas'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Los cuatro precios son obligatorios']);
            exit;
        }

        try {
            $pdo->beginTransaction();
            // sku_interno: no se solicita al usuario, usamos codigo_sae para cumplir NOT NULL
            $stmt = $pdo->prepare("INSERT INTO productos (codigo_sae, sku_interno, nombre_comercial, descripcion, unidad) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$codigo_sae, $codigo_sae, $nombre_comercial, $descripcion ?: null, $unidad ?: null]);
            $producto_id = $pdo->lastInsertId();

            $stmt2 = $pdo->prepare("INSERT INTO lista_precios (producto_id, precio_publico, precio_minimo, precio_materialista, precio_tiendas) VALUES (?, ?, ?, ?, ?)");
            $stmt2->execute([$producto_id, $precio_publico, $precio_minimo, $precio_materialista, $precio_tiendas]);
            $pdo->commit();
            echo json_encode(['success' => true, 'id' => (int)$producto_id]);
        } catch (PDOException $e) {
            $pdo->rollBack();
            if (strpos($e->getMessage(), 'Duplicate') !== false) {
                http_response_code(409);
                echo json_encode(['error' => 'El código SAE ya existe']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => $e->getMessage()]);
            }
        }
        break;

    case 'PUT':
        $id = (int)($input['id'] ?? 0);
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'ID inválido']);
            exit;
        }

        $codigo_sae = trim($input['codigo_sae'] ?? '');
        $nombre_comercial = trim($input['nombre_comercial'] ?? '');
        $descripcion = trim($input['descripcion'] ?? '');
        $unidad = trim($input['unidad'] ?? '');
        $precio_publico = (float)($input['precio_publico'] ?? 0);
        $precio_minimo = (float)($input['precio_minimo'] ?? 0);
        $precio_materialista = isset($input['precio_materialista']) ? (float)$input['precio_materialista'] : null;
        $precio_tiendas = isset($input['precio_tiendas']) ? (float)$input['precio_tiendas'] : null;

        if (empty($codigo_sae) || empty($nombre_comercial)) {
            http_response_code(400);
            echo json_encode(['error' => 'Código SAE y nombre comercial son obligatorios']);
            exit;
        }

        if ($precio_publico < 0 || $precio_minimo < 0 || $precio_materialista < 0 || $precio_tiendas < 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Todos los precios son obligatorios y no pueden ser negativos']);
            exit;
        }

        if (!isset($input['precio_materialista']) || !isset($input['precio_tiendas'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Los cuatro precios son obligatorios']);
            exit;
        }

        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("UPDATE productos SET codigo_sae = ?, nombre_comercial = ?, descripcion = ?, unidad = ? WHERE id = ?");
            $stmt->execute([$codigo_sae, $nombre_comercial, $descripcion ?: null, $unidad ?: null, $id]);

            // Desactivar precios anteriores y crear nuevo registro vigente
            $pdo->prepare("UPDATE lista_precios SET activo = 0 WHERE producto_id = ?")->execute([$id]);
            $stmt2 = $pdo->prepare("INSERT INTO lista_precios (producto_id, precio_publico, precio_minimo, precio_materialista, precio_tiendas) VALUES (?, ?, ?, ?, ?)");
            $stmt2->execute([$id, $precio_publico, $precio_minimo, $precio_materialista, $precio_tiendas]);
            $pdo->commit();
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            $pdo->rollBack();
            if (strpos($e->getMessage(), 'Duplicate') !== false) {
                http_response_code(409);
                echo json_encode(['error' => 'El código SAE ya existe para otro producto']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => $e->getMessage()]);
            }
        }
        break;

    case 'DELETE':
        $id = (int)($_GET['id'] ?? $input['id'] ?? 0);
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'ID inválido']);
            exit;
        }

        try {
            $pdo->beginTransaction();
            $pdo->prepare("DELETE FROM lista_precios WHERE producto_id = ?")->execute([$id]);
            $stmt = $pdo->prepare("DELETE FROM productos WHERE id = ?");
            $stmt->execute([$id]);
            $pdo->commit();
            if ($stmt->rowCount() === 0) {
                http_response_code(404);
                echo json_encode(['error' => 'Producto no encontrado']);
            } else {
                echo json_encode(['success' => true]);
            }
        } catch (PDOException $e) {
            $pdo->rollBack();
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
}
