<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requisições - PHP Health Monitor</title>
    <style>
        :root {
            --bg: #f5f7fb;
            --card: #ffffff;
            --border: #e5e7eb;
            --text: #111827;
            --muted: #6b7280;
            --brand: #4f46e5;
            --brand-weak: #eef2ff;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: var(--bg);
            color: var(--text);
        }

        .app {
            display: grid;
            grid-template-columns: 240px 1fr;
            min-height: 100vh;
        }

        .sidebar {
            background: var(--card);
            border-right: 1px solid var(--border);
            padding: 24px 16px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            color: var(--brand);
            margin-bottom: 28px;
        }

        .nav {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .nav a {
            text-decoration: none;
            color: var(--text);
            padding: 10px 12px;
            border-radius: 8px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav a.active {
            background: var(--brand-weak);
            color: var(--brand);
            font-weight: 600;
        }

        .content {
            padding: 28px 32px;
        }

        .page-title {
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .page-subtitle {
            color: var(--muted);
            font-size: 0.9rem;
            margin-bottom: 18px;
        }

        .filters {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 16px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 12px;
            align-items: end;
            margin-bottom: 18px;
        }

        .field label {
            display: block;
            font-size: 0.8rem;
            color: var(--muted);
            margin-bottom: 6px;
        }

        .field select,
        .field input {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: #fff;
            font-size: 0.9rem;
        }

        .btn {
            padding: 8px 12px;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: #fff;
            font-size: 0.9rem;
            cursor: pointer;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 12px;
            margin-bottom: 18px;
        }

        .stat {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 16px 18px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 6px 18px rgba(15, 23, 42, 0.06);
        }

        .stat::after {
            content: "";
            position: absolute;
            inset: 0;
            opacity: 0.08;
            background: linear-gradient(135deg, transparent 0%, rgba(79, 70, 229, 0.7) 100%);
            pointer-events: none;
        }

        .stat-total {
            border-color: #c7d2fe;
            background: linear-gradient(135deg, #eef2ff 0%, #ffffff 60%);
        }

        .stat-success {
            border-color: #bbf7d0;
            background: linear-gradient(135deg, #ecfdf5 0%, #ffffff 60%);
        }

        .stat-slow {
            border-color: #fde68a;
            background: linear-gradient(135deg, #fffbeb 0%, #ffffff 60%);
        }

        .stat-error {
            border-color: #fecaca;
            background: linear-gradient(135deg, #fef2f2 0%, #ffffff 60%);
        }

        .stat-label {
            color: var(--muted);
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .stat-value {
            font-size: 1.7rem;
            font-weight: 800;
            margin-top: 8px;
        }

        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 10px;
            overflow: hidden;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 16px;
            border-bottom: 1px solid var(--border);
        }

        .card-title {
            font-weight: 600;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
        }

        thead {
            background: #f9fafb;
        }

        th, td {
            text-align: left;
            padding: 10px 12px;
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }

        tr:hover {
            background: #f9fafb;
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .method-get { background: #e0f2fe; color: #0369a1; }
        .method-post { background: #dcfce7; color: #166534; }
        .method-put { background: #fef3c7; color: #92400e; }
        .method-delete { background: #fee2e2; color: #991b1b; }
        .method-patch { background: #f3e8ff; color: #6b21a8; }
        .method-head { background: #e0e7ff; color: #3730a3; }

        .status-2xx { background: #dcfce7; color: #166534; }
        .status-3xx { background: #e0f2fe; color: #0369a1; }
        .status-4xx { background: #fef3c7; color: #92400e; }
        .status-5xx { background: #fee2e2; color: #991b1b; }

        .duration-fast { color: var(--success); font-weight: 600; }
        .duration-normal { color: var(--warning); font-weight: 600; }
        .duration-slow { color: var(--danger); font-weight: 600; }

        .pagination {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 16px;
        }

        .pagination .btn {
            background: var(--brand);
            color: #fff;
            border: none;
        }

        .pagination .btn:disabled {
            background: #c7c7c7;
            cursor: not-allowed;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.4);
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-content {
            background: #fff;
            border-radius: 10px;
            width: 100%;
            max-width: 600px;
            overflow: hidden;
        }

        .modal-header,
        .modal-footer {
            padding: 14px 16px;
            border-bottom: 1px solid var(--border);
        }

        .modal-footer {
            border-top: 1px solid var(--border);
            border-bottom: none;
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }

        .modal-body {
            padding: 16px;
        }

        .detail {
            margin-bottom: 12px;
        }

        .detail-label {
            font-size: 0.75rem;
            color: var(--muted);
            margin-bottom: 4px;
            text-transform: uppercase;
        }

        .detail-value {
            background: #f9fafb;
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 8px 10px;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        }

        @media (max-width: 900px) {
            .app {
                grid-template-columns: 1fr;
            }
            .sidebar {
                border-right: none;
                border-bottom: 1px solid var(--border);
            }
        }
    </style>
</head>
<body>
    <div class="app">
        <aside class="sidebar">
            <div class="brand">⚡ PHP Health</div>
            <nav class="nav">
                <a class="active" href="#">Requests</a>
                <a href="#">Errors</a>
                <a href="#">Database</a>
                <a href="#">Performance</a>
            </nav>
        </aside>

        <main class="content">
            <div class="page-title">Requests</div>
            <div class="page-subtitle">Visão geral das requisições capturadas</div>

            <div class="filters">
                <div class="field">
                    <label for="method-filter">Método</label>
                    <select id="method-filter">
                        <option value="">Todos</option>
                        <option value="GET">GET</option>
                        <option value="POST">POST</option>
                        <option value="PUT">PUT</option>
                        <option value="DELETE">DELETE</option>
                        <option value="PATCH">PATCH</option>
                        <option value="HEAD">HEAD</option>
                    </select>
                </div>
                <div class="field">
                    <label for="status-filter">Status</label>
                    <select id="status-filter">
                        <option value="">Todos</option>
                        <option value="2">2xx</option>
                        <option value="3">3xx</option>
                        <option value="4">4xx</option>
                        <option value="5">5xx</option>
                    </select>
                </div>
                <div class="field">
                    <label for="search-filter">URI</label>
                    <input id="search-filter" type="text" placeholder="/api/users">
                </div>
                <button class="btn" id="clear-filters">Limpar</button>
            </div>

            <div class="stats">
                <div class="stat stat-total">
                    <div class="stat-label">Total</div>
                    <div class="stat-value" id="total-requests">0</div>
                </div>
                <div class="stat stat-success">
                    <div class="stat-label">Sucesso</div>
                    <div class="stat-value" id="success-requests">0</div>
                </div>
                <div class="stat stat-slow">
                    <div class="stat-label">Lentas</div>
                    <div class="stat-value" id="slow-requests">0</div>
                </div>
                <div class="stat stat-error">
                    <div class="stat-label">Erro</div>
                    <div class="stat-value" id="error-requests">0</div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-title">Histórico</div>
                    <div class="stat-label">Mostrando <span id="showing">0</span> de <span id="total">0</span></div>
                </div>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Método</th>
                                <th>URI</th>
                                <th>Status</th>
                                <th>IP</th>
                                <th>Duração</th>
                                <th>Memória</th>
                                <th>Data</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody id="requests-tbody">
                            <tr>
                                <td colspan="8">Carregando...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="pagination">
                    <div>
                        <select id="per-page" class="btn" style="background:#fff;color:#111;border:1px solid var(--border);">
                            <option value="10">10 por página</option>
                            <option value="25">25 por página</option>
                            <option value="50">50 por página</option>
                            <option value="100">100 por página</option>
                        </select>
                    </div>
                    <div>
                        <button class="btn" id="prev-btn" disabled>Anterior</button>
                        <span id="page-info" style="margin: 0 10px; color: var(--muted);">Página 1</span>
                        <button class="btn" id="next-btn">Próximo</button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <div id="detail-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <strong>Detalhes da Requisição</strong>
            </div>
            <div class="modal-body" id="modal-body"></div>
            <div class="modal-footer">
                <button class="btn" onclick="closeModal()">Fechar</button>
            </div>
        </div>
    </div>

    <script>
        const serverRequests = @json($requests ?? []);
        let allRequests = [];
        let currentPage = 1;
        let itemsPerPage = 10;
        let filteredRequests = [];

        function processRequests(data) {
            if (!Array.isArray(data)) {
                return [];
            }

            return data.map(req => {
                const requestData = req.data?.data || req.data || {};
                const method = requestData.method || req.method || 'UNKNOWN';
                const uri = requestData.uri || req.uri || '/unknown';
                const status = requestData.status_code || req.status_code || 200;
                const ip = requestData.ip || req.ip || '0.0.0.0';
                const duration = requestData.duration || req.duration || 0;
                const memory = requestData.memory || req.memory || 0;
                const timestamp = requestData.timestamp || req.timestamp;

                let formattedTime = '';
                if (timestamp) {
                    const date = new Date(timestamp * 1000);
                    formattedTime = date.toLocaleString('pt-BR');
                }

                return {
                    method,
                    uri,
                    status,
                    ip,
                    duration,
                    memory,
                    timestamp: formattedTime || new Date().toLocaleString('pt-BR')
                };
            });
        }

        function loadRequests() {
            allRequests = processRequests(serverRequests);
            updateStats();
            applyFilters();
        }

        function updateStats() {
            const total = allRequests.length;
            const success = allRequests.filter(r => r.status < 400).length;
            const slow = allRequests.filter(r => r.duration > 1000).length;
            const error = allRequests.filter(r => r.status >= 400).length;

            document.getElementById('total-requests').textContent = total;
            document.getElementById('success-requests').textContent = success;
            document.getElementById('slow-requests').textContent = slow;
            document.getElementById('error-requests').textContent = error;
        }

        function applyFilters() {
            const method = document.getElementById('method-filter').value;
            const status = document.getElementById('status-filter').value;
            const search = document.getElementById('search-filter').value.toLowerCase();

            filteredRequests = allRequests.filter(req => {
                const methodMatch = !method || req.method === method;
                const statusMatch = !status || req.status.toString().startsWith(status);
                const searchMatch = !search || req.uri.toLowerCase().includes(search);
                return methodMatch && statusMatch && searchMatch;
            });

            currentPage = 1;
            renderTable();
        }

        function renderTable() {
            const start = (currentPage - 1) * itemsPerPage;
            const end = start + itemsPerPage;
            const paginatedRequests = filteredRequests.slice(start, end);

            const tbody = document.getElementById('requests-tbody');
            tbody.innerHTML = '';

            if (paginatedRequests.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8">Nenhuma requisição encontrada</td></tr>';
            } else {
                paginatedRequests.forEach(req => {
                    const methodClass = `method-${req.method.toLowerCase()}`;
                    const statusClass = `status-${req.status.toString()[0]}xx`;
                    const durationClass = req.duration > 1000 ? 'duration-slow' : (req.duration > 500 ? 'duration-normal' : 'duration-fast');

                    const row = `
                        <tr>
                            <td><span class="badge ${methodClass}">${req.method}</span></td>
                            <td>${escapeHtml(req.uri)}</td>
                            <td><span class="badge ${statusClass}">${req.status}</span></td>
                            <td>${escapeHtml(req.ip)}</td>
                            <td><span class="${durationClass}">${req.duration.toFixed(1)}ms</span></td>
                            <td>${(req.memory / 1024 / 1024).toFixed(2)} MB</td>
                            <td>${req.timestamp}</td>
                            <td><button class="btn" onclick="showDetails('${escapeHtml(req.uri)}', '${req.method}', ${req.status}, '${escapeHtml(req.ip)}', ${req.duration}, ${req.memory})">Detalhes</button></td>
                        </tr>
                    `;
                    tbody.innerHTML += row;
                });
            }

            updatePagination();
            document.getElementById('showing').textContent = Math.min(end, filteredRequests.length);
            document.getElementById('total').textContent = filteredRequests.length;
        }

        function updatePagination() {
            const totalPages = Math.ceil(filteredRequests.length / itemsPerPage);
            document.getElementById('prev-btn').disabled = currentPage === 1;
            document.getElementById('next-btn').disabled = currentPage === totalPages || totalPages === 0;
            document.getElementById('page-info').textContent = `Página ${currentPage} de ${totalPages || 1}`;
        }

        function showDetails(uri, method, status, ip, duration, memory) {
            const modal = document.getElementById('detail-modal');
            const body = document.getElementById('modal-body');
            const statusLabel = status < 400 ? 'Sucesso' : (status < 500 ? 'Aviso' : 'Erro');
            const durationLabel = duration > 1000 ? 'Lento' : 'Normal';

            body.innerHTML = `
                <div class="detail">
                    <div class="detail-label">Método</div>
                    <div class="detail-value">${method}</div>
                </div>
                <div class="detail">
                    <div class="detail-label">URI</div>
                    <div class="detail-value">${escapeHtml(uri)}</div>
                </div>
                <div class="detail">
                    <div class="detail-label">Status</div>
                    <div class="detail-value">${status} - ${statusLabel}</div>
                </div>
                <div class="detail">
                    <div class="detail-label">IP</div>
                    <div class="detail-value">${escapeHtml(ip)}</div>
                </div>
                <div class="detail">
                    <div class="detail-label">Duração</div>
                    <div class="detail-value">${duration.toFixed(2)}ms - ${durationLabel}</div>
                </div>
                <div class="detail">
                    <div class="detail-label">Memória</div>
                    <div class="detail-value">${(memory / 1024 / 1024).toFixed(2)} MB</div>
                </div>
            `;

            modal.style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('detail-modal').style.display = 'none';
        }

        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return String(text).replace(/[&<>"']/g, m => map[m]);
        }

        document.getElementById('method-filter').addEventListener('change', applyFilters);
        document.getElementById('status-filter').addEventListener('change', applyFilters);
        document.getElementById('search-filter').addEventListener('input', applyFilters);
        document.getElementById('clear-filters').addEventListener('click', () => {
            document.getElementById('method-filter').value = '';
            document.getElementById('status-filter').value = '';
            document.getElementById('search-filter').value = '';
            applyFilters();
        });

        document.getElementById('per-page').addEventListener('change', (e) => {
            itemsPerPage = parseInt(e.target.value, 10);
            currentPage = 1;
            renderTable();
        });

        document.getElementById('prev-btn').addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                renderTable();
            }
        });

        document.getElementById('next-btn').addEventListener('click', () => {
            const totalPages = Math.ceil(filteredRequests.length / itemsPerPage);
            if (currentPage < totalPages) {
                currentPage++;
                renderTable();
            }
        });

        window.addEventListener('click', (e) => {
            const modal = document.getElementById('detail-modal');
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });

        loadRequests();
    </script>
</body>
</html>