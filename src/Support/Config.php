<?php

declare(strict_types=1);

namespace PHPHealth\Monitor\Support;

/**
 * Gerenciador de configurações
 */
class Config
{
    /** @var array<string, mixed> */
    private array $config;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(array $config = [])
    {
        $this->config = array_replace_recursive($this->getDefaults(), $config);
    }

    /**
     * Obtém um valor de configuração
     *
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        $keys = explode('.', $key);
        $value = $this->config;

        foreach ($keys as $k) {
            if (! isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }

        return $value;
    }

    /**
     * Define um valor de configuração
     *
     * @param mixed $value
     */
    public function set(string $key, $value): void
    {
        $keys = explode('.', $key);
        $config = &$this->config;

        foreach ($keys as $k) {
            if (! isset($config[$k]) || ! is_array($config[$k])) {
                $config[$k] = [];
            }
            $config = &$config[$k];
        }

        $config = $value;
    }

    /**
     * Obtém toda a configuração
     *
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $this->config;
    }

    /**
     * Configurações padrão
     *
     * @return array<string, mixed>
     */
    private function getDefaults(): array
    {
        return [
            'storage' => [
                'driver' => 'sqlite',
                'database_path' => sys_get_temp_dir() . '/health-monitor.db',
                'cleanup_days' => 7,
            ],
            'collectors' => [
                'request' => [
                    'enabled' => true,
                    'slow_threshold' => 1000, // ms
                ],
                'database' => [
                    'enabled' => true,
                    'slow_query_threshold' => 100, // ms
                ],
                'error' => [
                    'enabled' => true,
                ],
                'memory' => [
                    'enabled' => true,
                ],
            ],
            'alerts' => [
                'enabled' => false,
                'channels' => [],
            ],
            'dashboard' => [
                'enabled' => true,
                'path' => '/health-monitor',
                'auth' => [
                    'enabled' => true,
                    'username' => 'admin',
                    'password' => password_hash('admin', PASSWORD_BCRYPT),
                ],
            ],
            'performance' => [
                'buffer_size' => 100,
                'async' => false,
            ],
        ];
    }
}
