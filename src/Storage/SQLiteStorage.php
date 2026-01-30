<?php

declare(strict_types=1);

namespace PHPHealth\Monitor\Storage;

use PHPHealth\Monitor\Contracts\StorageInterface;
use PHPHealth\Monitor\Support\Config;
use PDO;

/**
 * Implementação de storage usando SQLite
 */
class SQLiteStorage implements StorageInterface
{
    private Config $config;
    private ?PDO $pdo = null;

    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->initialize();
    }

    /**
     * @param array<string, mixed> $data
     */
    public function store(array $data): bool
    {
        try {
            $pdo = $this->getPdo();

            // Armazena dados da requisição
            if (isset($data['request'])) {
                $stmt = $pdo->prepare("
                    INSERT INTO requests (method, uri, status_code, duration, memory, memory_peak, ip, user_agent, is_slow, created_at)
                    VALUES (:method, :uri, :status_code, :duration, :memory, :memory_peak, :ip, :user_agent, :is_slow, :created_at)
                ");

                $stmt->execute([
                    'method' => $data['request']['method'] ?? null,
                    'uri' => $data['request']['uri'] ?? null,
                    'status_code' => $data['request']['status_code'] ?? null,
                    'duration' => $data['request']['duration'] ?? null,
                    'memory' => $data['request']['memory'] ?? null,
                    'memory_peak' => $data['request']['memory_peak'] ?? null,
                    'ip' => $data['request']['ip'] ?? null,
                    'user_agent' => $data['request']['user_agent'] ?? null,
                    'is_slow' => $data['request']['is_slow'] ? 1 : 0,
                    'created_at' => $data['request']['timestamp'] ?? time(),
                ]);

                $requestId = $pdo->lastInsertId();

                // Armazena queries SQL relacionadas
                if (isset($data['database']['queries'])) {
                    $stmt = $pdo->prepare("
                        INSERT INTO queries (request_id, sql, duration, created_at)
                        VALUES (:request_id, :sql, :duration, :created_at)
                    ");

                    foreach ($data['database']['queries'] as $query) {
                        $stmt->execute([
                            'request_id' => $requestId,
                            'sql' => $query['sql'] ?? null,
                            'duration' => $query['duration'] ?? null,
                            'created_at' => time(),
                        ]);
                    }
                }

                // Armazena erros
                if (isset($data['error']['errors'])) {
                    $stmt = $pdo->prepare("
                        INSERT INTO errors (request_id, type, level, message, file, line, trace, created_at)
                        VALUES (:request_id, :type, :level, :message, :file, :line, :trace, :created_at)
                    ");

                    foreach ($data['error']['errors'] as $error) {
                        $stmt->execute([
                            'request_id' => $requestId,
                            'type' => $error['type'] ?? null,
                            'level' => $error['level_name'] ?? $error['class'] ?? null,
                            'message' => $error['message'] ?? null,
                            'file' => $error['file'] ?? null,
                            'line' => $error['line'] ?? null,
                            'trace' => $error['trace'] ?? null,
                            'created_at' => $error['timestamp'] ?? time(),
                        ]);
                    }
                }
            }

            return true;
        } catch (\PDOException $e) {
            error_log("PHP Health Monitor Storage Error: " . $e->getMessage());

            return false;
        }
    }

    /**
     * @param array<string, mixed> $filters
     * @return array<int, mixed>
     */
    public function retrieve(array $filters = []): array
    {
        $pdo = $this->getPdo();

        $sql = "SELECT * FROM requests WHERE 1=1";
        $params = [];

        // Aplicar filtros
        if (isset($filters['start_date'])) {
            $sql .= " AND created_at >= :start_date";
            $params['start_date'] = $filters['start_date'];
        }

        if (isset($filters['end_date'])) {
            $sql .= " AND created_at <= :end_date";
            $params['end_date'] = $filters['end_date'];
        }

        if (isset($filters['is_slow'])) {
            $sql .= " AND is_slow = :is_slow";
            $params['is_slow'] = $filters['is_slow'] ? 1 : 0;
        }

        $sql .= " ORDER BY created_at DESC LIMIT " . ($filters['limit'] ?? 100);

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function cleanup(int $days = 7): int
    {
        $pdo = $this->getPdo();
        $cutoff = time() - ($days * 86400);

        $stmt = $pdo->prepare("DELETE FROM requests WHERE created_at < :cutoff");
        $stmt->execute(['cutoff' => $cutoff]);

        return $stmt->rowCount();
    }

    /**
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function getStats(string $metric, array $filters = []): array
    {
        // TODO: Implementar estatísticas agregadas
        return [];
    }

    /**
     * Inicializa o banco de dados
     */
    private function initialize(): void
    {
        $pdo = $this->getPdo();

        // Cria tabelas se não existirem
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS requests (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                method TEXT,
                uri TEXT,
                status_code INTEGER,
                duration REAL,
                memory INTEGER,
                memory_peak INTEGER,
                ip TEXT,
                user_agent TEXT,
                is_slow INTEGER DEFAULT 0,
                created_at INTEGER
            )
        ");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS queries (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                request_id INTEGER,
                sql TEXT,
                duration REAL,
                created_at INTEGER,
                FOREIGN KEY (request_id) REFERENCES requests(id) ON DELETE CASCADE
            )
        ");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS errors (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                request_id INTEGER,
                type TEXT,
                level TEXT,
                message TEXT,
                file TEXT,
                line INTEGER,
                trace TEXT,
                created_at INTEGER,
                FOREIGN KEY (request_id) REFERENCES requests(id) ON DELETE CASCADE
            )
        ");

        // Cria índices
        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_requests_created_at ON requests(created_at)");
        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_requests_is_slow ON requests(is_slow)");
        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_queries_request_id ON queries(request_id)");
        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_errors_request_id ON errors(request_id)");
    }

    /**
     * Obtém a conexão PDO
     */
    private function getPdo(): PDO
    {
        if ($this->pdo === null) {
            $path = $this->config->get('storage.database_path');

            // Cria diretório se não existir
            $dir = dirname($path);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $this->pdo = new PDO('sqlite:' . $path);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return $this->pdo;
    }
}
