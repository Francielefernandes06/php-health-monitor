<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Health Monitor - Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .header h1 {
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .header p {
            opacity: 0.9;
            font-size: 0.95rem;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.12);
        }

        .stat-label {
            font-size: 0.875rem;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 0.25rem;
        }

        .stat-trend {
            font-size: 0.875rem;
            color: #10b981;
        }

        .stat-trend.down {
            color: #ef4444;
        }

        .card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f0f0f0;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #333;
        }

        .badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #f9fafb;
        }

        th {
            text-align: left;
            padding: 0.75rem 1rem;
            font-weight: 600;
            font-size: 0.875rem;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        td {
            padding: 1rem;
            border-bottom: 1px solid #f0f0f0;
        }

        tr:hover {
            background: #f9fafb;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 0.5rem;
        }

        .status-dot.success {
            background: #10b981;
        }

        .status-dot.warning {
            background: #f59e0b;
        }

        .status-dot.error {
            background: #ef4444;
        }

        .btn {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background: #5568d3;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #666;
        }

        .empty-state-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.3;
        }

        .loading {
            text-align: center;
            padding: 2rem;
            color: #666;
        }

        .footer {
            text-align: center;
            padding: 2rem;
            color: #666;
            font-size: 0.875rem;
        }

        .refresh-indicator {
            display: inline-block;
            margin-left: 0.5rem;
            color: #10b981;
            font-size: 0.875rem;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .spinning {
            animation: spin 1s linear infinite;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <h1>🏥 PHP Health Monitor</h1>
            <p>Application Performance Monitoring Dashboard</p>
        </div>
    </div>

    <div class="container">
        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Avg Response Time</div>
                <div class="stat-value" id="avg-response-time">--</div>
                <div class="stat-trend">Loading...</div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Requests/Min</div>
                <div class="stat-value" id="requests-per-min">--</div>
                <div class="stat-trend">Loading...</div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Error Rate</div>
                <div class="stat-value" id="error-rate">--</div>
                <div class="stat-trend">Loading...</div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Memory Usage</div>
                <div class="stat-value" id="memory-usage">--</div>
                <div class="stat-trend">Loading...</div>
            </div>
        </div>

        <!-- Recent Requests -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Recent Requests</h2>
                <span class="refresh-indicator" id="last-update">Loading...</span>
            </div>

            <div id="requests-container">
                <div class="loading">Loading requests...</div>
            </div>
        </div>

        <!-- Slow Queries -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Slow Queries</h2>
                <span class="badge badge-warning">Requires Attention</span>
            </div>

            <div id="queries-container">
                <div class="empty-state">
                    <div class="empty-state-icon">🎉</div>
                    <p>No slow queries detected!</p>
                    <p style="font-size: 0.875rem; margin-top: 0.5rem; color: #999;">
                        Queries taking longer than 100ms will appear here
                    </p>
                </div>
            </div>
        </div>

        <!-- Recent Errors -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Recent Errors</h2>
                <span class="badge badge-success" id="error-badge">No Errors</span>
            </div>

            <div id="errors-container">
                <div class="empty-state">
                    <div class="empty-state-icon">✅</div>
                    <p>No errors detected!</p>
                    <p style="font-size: 0.875rem; margin-top: 0.5rem; color: #999;">
                        Your application is running smoothly
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>PHP Health Monitor v1.0.0-dev | Open Source APM for PHP</p>
        <p style="margin-top: 0.5rem;">
            <a href="https://github.com/Francielefernandes06/php-health-monitor" style="color: #667eea; text-decoration: none;">
                Documentation
            </a>
        </p>
    </div>

    <script>
        // API Base URL
        const API_BASE = '/health-monitor/api';

        // Auto-refresh interval (30 seconds)
        const REFRESH_INTERVAL = 30000;

        // Load dashboard data
        async function loadDashboard() {
            try {
                // Load stats
                const statsResponse = await fetch(`${API_BASE}/stats`);
                const statsData = await statsResponse.json();
                
                if (statsData.success) {
                    updateStats(statsData.data);
                }

                // Load recent requests
                const requestsResponse = await fetch(`${API_BASE}/requests?limit=10`);
                const requestsData = await requestsResponse.json();
                
                if (requestsData.success) {
                    updateRequests(requestsData.data);
                }

                // Update last refresh time
                document.getElementById('last-update').textContent = 
                    'Updated ' + new Date().toLocaleTimeString();

            } catch (error) {
                console.error('Error loading dashboard:', error);
                showError('Failed to load dashboard data');
            }
        }

        // Update stats cards
        function updateStats(stats) {
            document.getElementById('avg-response-time').textContent = 
                Math.round(stats.avg_response_time || 0) + 'ms';
            
            document.getElementById('requests-per-min').textContent = 
                Math.round(stats.requests_per_min || 0);
            
            document.getElementById('error-rate').textContent = 
                (stats.error_rate || 0).toFixed(1) + '%';
            
            document.getElementById('memory-usage').textContent = 
                formatBytes(stats.memory_avg || 0);
        }

        // Update requests table
        function updateRequests(requests) {
            const container = document.getElementById('requests-container');
            
            if (!requests || requests.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <div class="empty-state-icon">📭</div>
                        <p>No requests recorded yet</p>
                        <p style="font-size: 0.875rem; margin-top: 0.5rem; color: #999;">
                            Make some requests to your application to see data here
                        </p>
                    </div>
                `;
                return;
            }

            let html = `
                <table>
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>Method</th>
                            <th>URI</th>
                            <th>Status</th>
                            <th>Duration</th>
                            <th>Memory</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            requests.forEach(request => {
                const statusClass = request.status_code >= 500 ? 'error' : 
                                  request.status_code >= 400 ? 'warning' : 'success';
                
                const time = new Date(request.created_at * 1000).toLocaleTimeString();
                
                html += `
                    <tr>
                        <td>${time}</td>
                        <td><strong>${request.method || 'GET'}</strong></td>
                        <td>${request.uri || '/'}</td>
                        <td>
                            <span class="status-dot ${statusClass}"></span>
                            ${request.status_code || 200}
                        </td>
                        <td>${Math.round(request.duration || 0)}ms</td>
                        <td>${formatBytes(request.memory || 0)}</td>
                    </tr>
                `;
            });

            html += '</tbody></table>';
            container.innerHTML = html;
        }

        // Format bytes to human readable
        function formatBytes(bytes) {
            if (bytes === 0) return '0 B';
            const k = 1024;
            const sizes = ['B', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + ' ' + sizes[i];
        }

        // Show error message
        function showError(message) {
            const container = document.getElementById('requests-container');
            container.innerHTML = `
                <div class="empty-state">
                    <div class="empty-state-icon">⚠️</div>
                    <p>${message}</p>
                    <button class="btn btn-primary" onclick="loadDashboard()">
                        Retry
                    </button>
                </div>
            `;
        }

        // Initial load
        loadDashboard();

        // Auto-refresh
        setInterval(loadDashboard, REFRESH_INTERVAL);
    </script>
</body>
</html>