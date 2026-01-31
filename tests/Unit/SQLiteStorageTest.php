<?php

declare(strict_types=1);

namespace PHPHealth\Monitor\Tests\Unit;

use PHPHealth\Monitor\Storage\SQLiteStorage;
use PHPHealth\Monitor\Support\Config;
use PHPUnit\Framework\TestCase;

class SQLiteStorageTest extends TestCase
{
    private $dbPath;
    private $storage;

    protected function setUp(): void
    {
        $this->dbPath = __DIR__ . '/../Fixtures/sqlite-storage-test.db';
        if (file_exists($this->dbPath)) {
            unlink($this->dbPath);
        }
        $config = new Config([
            'storage' => [
                'driver' => 'sqlite',
                'database_path' => $this->dbPath,
            ],
        ]);
        $this->storage = new SQLiteStorage($config);
    }

    protected function tearDown(): void
    {
        if (file_exists($this->dbPath)) {
            unlink($this->dbPath);
        }
    }

    public function test_store_and_retrieve(): void
    {
        $data = [
            'request' => [
                'method' => 'GET',
                'uri' => '/test',
                'status_code' => 200,
                'duration' => 123,
                'memory' => 1000,
                'memory_peak' => 2000,
                'ip' => '127.0.0.1',
                'user_agent' => 'PHPUnit',
                'is_slow' => false,
                'timestamp' => time(),
            ],
        ];
        $result = $this->storage->store($data);
        $this->assertTrue($result);
        $retrieved = $this->storage->retrieve(['limit' => 1]);
        $this->assertCount(1, $retrieved);
        $this->assertEquals('GET', $retrieved[0]['method']);
    }

    public function test_cleanup(): void
    {
        $affected = $this->storage->cleanup(0); // Remove tudo
        $this->assertIsInt($affected);
    }

    public function test_get_stats_returns_empty(): void
    {
        $stats = $this->storage->getStats('any');
        $this->assertIsArray($stats);
        $this->assertEmpty($stats);
    }
}
