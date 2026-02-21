<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Productos y Precios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --nav-bg: #1a1d23;
            --accent: #3b82f6;
            --accent-hover: #2563eb;
        }
        body { background: #f1f5f9; min-height: 100vh; }
        .navbar-custom {
            background: var(--nav-bg) !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.15);
        }
        .navbar-custom .navbar-brand { color: #fff; font-weight: 600; }
        .btn-add { background: var(--accent); color: #fff; border: none; }
        .btn-add:hover { background: var(--accent-hover); color: #fff; }
        .card-list { border: none; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); }
        .table-productos { font-size: 0.95rem; }
        .table-productos tbody tr:hover { background: #f8fafc; }
        .badge-codigo { font-family: monospace; background: #e2e8f0; color: #475569; }
        .modal-header { border-bottom: 1px solid #e2e8f0; }
        .modal-footer { border-top: 1px solid #e2e8f0; }
        .form-label { font-weight: 500; color: #334155; }
        .empty-state { padding: 4rem 2rem; text-align: center; color: #64748b; }
        .empty-state i { font-size: 3rem; opacity: 0.5; }
        .btn-action { padding: 0.25rem 0.5rem; }
        .th-sortable { cursor: pointer; user-select: none; white-space: nowrap; }
        .th-sortable:hover { background: #e2e8f0 !important; }
        .th-sortable .bi-sort { opacity: 0.3; }
        .th-sortable.asc .bi-sort-down-alt, .th-sortable.desc .bi-sort-down { opacity: 1; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><i class="bi bi-box-seam me-2"></i>Productos y Precios</a>
            <div class="d-flex">
                <button type="button" class="btn btn-add" data-bs-toggle="modal" data-bs-target="#modalProducto">
                    <i class="bi bi-plus-lg me-1"></i> Nuevo producto
                </button>
            </div>
        </div>
    </nav>

    <main class="container py-4">
        <div class="card card-list">
            <div class="card-header bg-white border-0 py-3">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <h5 class="mb-0 fw-semibold"><i class="bi bi-list-ul me-2"></i>Lista de productos con precios</h5>
                    <div class="input-group" style="max-width: 320px;">
                        <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" class="form-control" id="inputBuscar" placeholder="Buscar por código, nombre o unidad...">
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div id="loading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted">Cargando...</p>
                </div>
                <div id="empty" class="empty-state d-none">
                    <i class="bi bi-inbox d-block mb-3"></i>
                    <p class="mb-0">No hay productos. Agrega el primero desde el menú superior.</p>
                </div>
                <div id="tableWrap" class="d-none overflow-auto">
                    <table class="table table-productos table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="th-sortable" data-sort="codigo_sae">Código SAE <i class="bi bi-sort-down-alt ms-1"></i></th>
                                <th class="th-sortable" data-sort="nombre_comercial">Nombre comercial <i class="bi bi-sort-down-alt ms-1"></i></th>
                                <th class="th-sortable" data-sort="unidad">Unidad <i class="bi bi-sort-down-alt ms-1"></i></th>
                                <th class="th-sortable" data-sort="precio_publico">P. público <i class="bi bi-sort-down-alt ms-1"></i></th>
                                <th class="th-sortable" data-sort="precio_minimo">P. mínimo <i class="bi bi-sort-down-alt ms-1"></i></th>
                                <th class="th-sortable" data-sort="precio_materialista">P. materialista <i class="bi bi-sort-down-alt ms-1"></i></th>
                                <th class="th-sortable" data-sort="precio_tiendas">P. tiendas <i class="bi bi-sort-down-alt ms-1"></i></th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyProductos"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal Nuevo / Editar Producto -->
    <div class="modal fade" id="modalProducto" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Nuevo producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formProducto">
                    <input type="hidden" id="productoId" name="id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="codigo_sae" class="form-label">Código SAE <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="codigo_sae" name="codigo_sae" required placeholder="Ej: PROD-001">
                        </div>
                        <div class="mb-3">
                            <label for="nombre_comercial" class="form-label">Nombre comercial <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nombre_comercial" name="nombre_comercial" required>
                        </div>
                        <div class="mb-3">
                            <label for="unidad" class="form-label">Unidad</label>
                            <input type="text" class="form-control" id="unidad" name="unidad" placeholder="Pza, Kg, etc.">
                        </div>
                        <hr>
                        <h6 class="mb-3">Precios</h6>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label for="precio_publico" class="form-label">Precio público <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="precio_publico" name="precio_publico" step="0.01" min="0" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label for="precio_minimo" class="form-label">Precio mínimo <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="precio_minimo" name="precio_minimo" step="0.01" min="0" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label for="precio_materialista" class="form-label">Precio materialista <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="precio_materialista" name="precio_materialista" step="0.01" min="0" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label for="precio_tiendas" class="form-label">Precio tiendas <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="precio_tiendas" name="precio_tiendas" step="0.01" min="0" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-add">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const API = 'api/productos.php';
        const loading = document.getElementById('loading');
        const empty = document.getElementById('empty');
        const tableWrap = document.getElementById('tableWrap');
        const tbody = document.getElementById('tbodyProductos');
        const form = document.getElementById('formProducto');
        const modalEl = document.getElementById('modalProducto');
        const modalTitle = document.getElementById('modalTitle');
        const inputBuscar = document.getElementById('inputBuscar');

        let productosData = [];
        let sortCampo = 'nombre_comercial';
        let sortAsc = true;

        function formatPrecio(n) {
            if (n == null || n === '' || isNaN(n)) return '-';
            return parseFloat(n).toLocaleString('es-MX', { minimumFractionDigits: 2 });
        }

        function filtrarYOrdenar() {
            const q = (inputBuscar.value || '').trim().toLowerCase();
            let data = productosData;
            if (q) {
                data = data.filter(p => {
                    const codigo = (p.codigo_sae || '').toLowerCase();
                    const nombre = (p.nombre_comercial || '').toLowerCase();
                    const unidad = (p.unidad || '').toLowerCase();
                    return codigo.includes(q) || nombre.includes(q) || unidad.includes(q);
                });
            }
            data = [...data].sort((a, b) => {
                const c = sortCampo;
                let va = a[c], vb = b[c];
                if (c.startsWith('precio_')) {
                    va = parseFloat(va) || 0;
                    vb = parseFloat(vb) || 0;
                    return sortAsc ? va - vb : vb - va;
                }
                va = String(va || '').toLowerCase();
                vb = String(vb || '').toLowerCase();
                const cmp = va.localeCompare(vb);
                return sortAsc ? cmp : -cmp;
            });
            return data;
        }

        function renderizarTabla(data) {
            if (!data.length) {
                empty.classList.remove('d-none');
                empty.innerHTML = '<i class="bi bi-search d-block mb-3"></i><p class="mb-0">No se encontraron productos.</p>';
                tableWrap.classList.add('d-none');
                return;
            }
            empty.classList.add('d-none');
            tableWrap.classList.remove('d-none');
            tbody.innerHTML = data.map(p => `
                <tr data-id="${p.id}">
                    <td><span class="badge badge-codigo">${escapeHtml(p.codigo_sae || '-')}</span></td>
                    <td>${escapeHtml(p.nombre_comercial)}</td>
                    <td>${escapeHtml(p.unidad || '-')}</td>
                    <td>$${formatPrecio(p.precio_publico)}</td>
                    <td>$${formatPrecio(p.precio_minimo)}</td>
                    <td>$${formatPrecio(p.precio_materialista)}</td>
                    <td>$${formatPrecio(p.precio_tiendas)}</td>
                    <td class="text-end">
                        <button type="button" class="btn btn-sm btn-outline-primary btn-action" onclick="editarProducto(${p.id})" title="Editar"><i class="bi bi-pencil"></i></button>
                        <button type="button" class="btn btn-sm btn-outline-danger btn-action" onclick="confirmarEliminar(${p.id}, '${escapeHtml(p.nombre_comercial).replace(/'/g, "\\'")}')" title="Eliminar"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>
            `).join('');
        }

        function actualizarIndicadorOrden() {
            document.querySelectorAll('.th-sortable').forEach(th => {
                th.classList.remove('asc', 'desc');
                th.querySelector('i')?.remove();
                const icon = document.createElement('i');
                icon.className = 'bi ms-1';
                if (th.dataset.sort === sortCampo) {
                    th.classList.add(sortAsc ? 'asc' : 'desc');
                    icon.className += sortAsc ? ' bi-sort-down-alt' : ' bi-sort-down';
                } else {
                    icon.className += ' bi-sort-down-alt';
                    icon.style.opacity = '0.3';
                }
                th.appendChild(icon);
            });
        }

        function cargarProductos() {
            loading.classList.remove('d-none');
            empty.classList.add('d-none');
            tableWrap.classList.add('d-none');

            fetch(API)
                .then(r => r.json())
                .then(data => {
                    loading.classList.add('d-none');
                    productosData = Array.isArray(data) ? data : [];
                    if (!productosData.length) {
                        empty.classList.remove('d-none');
                        empty.innerHTML = '<i class="bi bi-inbox d-block mb-3"></i><p class="mb-0">No hay productos. Agrega el primero desde el menú superior.</p>';
                        return;
                    }
                    actualizarIndicadorOrden();
                    renderizarTabla(filtrarYOrdenar());
                })
                .catch(err => {
                    loading.classList.add('d-none');
                    empty.classList.remove('d-none');
                    empty.innerHTML = '<i class="bi bi-exclamation-triangle d-block mb-3 text-warning"></i><p>Error al cargar productos. Verifica la conexión a la base de datos.</p>';
                });
        }

        function escapeHtml(s) {
            const div = document.createElement('div');
            div.textContent = s;
            return div.innerHTML;
        }

        function abrirModalNuevo() {
            modalTitle.textContent = 'Nuevo producto';
            form.reset();
            document.getElementById('productoId').value = '';
        }

        function editarProducto(id) {
            fetch(API + '?id=' + id)
                .then(r => r.json())
                .then(p => {
                    if (p.error) { alert(p.error); return; }
                    modalTitle.textContent = 'Editar producto';
                    document.getElementById('productoId').value = p.id;
                    document.getElementById('codigo_sae').value = p.codigo_sae || '';
                    document.getElementById('nombre_comercial').value = p.nombre_comercial || '';
                    document.getElementById('unidad').value = p.unidad || '';
                    document.getElementById('precio_publico').value = p.precio_publico ?? '';
                    document.getElementById('precio_minimo').value = p.precio_minimo ?? '';
                    document.getElementById('precio_materialista').value = p.precio_materialista ?? '';
                    document.getElementById('precio_tiendas').value = p.precio_tiendas ?? '';
                    new bootstrap.Modal(modalEl).show();
                })
                .catch(() => alert('Error al cargar producto'));
        }

        function confirmarEliminar(id, nombre) {
            if (!confirm('¿Eliminar el producto "' + nombre + '"? Esta acción no se puede deshacer.')) return;
            fetch(API + '?id=' + id, { method: 'DELETE' })
                .then(r => r.json())
                .then(data => {
                    if (data.error) alert(data.error);
                    else cargarProductos();
                })
                .catch(() => alert('Error al eliminar'));
        }

        modalEl.addEventListener('show.bs.modal', function(e) {
            if (e.relatedTarget && e.relatedTarget.classList.contains('btn-add')) abrirModalNuevo();
        });

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('productoId').value;
            const payload = {
                codigo_sae: document.getElementById('codigo_sae').value.trim(),
                nombre_comercial: document.getElementById('nombre_comercial').value.trim(),
                unidad: document.getElementById('unidad').value.trim(),
                precio_publico: parseFloat(document.getElementById('precio_publico').value) || 0,
                precio_minimo: parseFloat(document.getElementById('precio_minimo').value) || 0,
                precio_materialista: parseFloat(document.getElementById('precio_materialista').value) || 0,
                precio_tiendas: parseFloat(document.getElementById('precio_tiendas').value) || 0
            };

            const url = API;
            const opts = {
                method: id ? 'PUT' : 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(id ? { ...payload, id } : payload)
            };

            fetch(url, opts)
                .then(r => r.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                    } else {
                        bootstrap.Modal.getInstance(modalEl).hide();
                        form.reset();
                        cargarProductos();
                    }
                })
                .catch(() => alert('Error al guardar'));
        });

        document.querySelector('[data-bs-target="#modalProducto"]').addEventListener('click', abrirModalNuevo);

        inputBuscar.addEventListener('input', () => renderizarTabla(filtrarYOrdenar()));

        document.querySelectorAll('.th-sortable').forEach(th => {
            th.addEventListener('click', () => {
                const campo = th.dataset.sort;
                if (sortCampo === campo) sortAsc = !sortAsc;
                else { sortCampo = campo; sortAsc = true; }
                actualizarIndicadorOrden();
                renderizarTabla(filtrarYOrdenar());
            });
        });

        cargarProductos();
    </script>
</body>
</html>
