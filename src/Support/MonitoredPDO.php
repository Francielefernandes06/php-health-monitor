<?php

namespace PHPHealth\Monitor\Support;

use PDO;
use PHPHealth\Monitor\Collectors\DatabaseCollector;

class MonitoredPDO extends PDO
{
    /** @var DatabaseCollector|null */
    private $collector;
    /** @var bool */
    private $monitorEnabled = true;

    public function __construct($dsn, $username = null, $passwd = null, $options = [], DatabaseCollector $collector = null, $monitorEnabled = true)
    {
        parent::__construct($dsn, $username, $passwd, $options);
        $this->collector = $collector;
        $this->monitorEnabled = $monitorEnabled;
    }

    public function query($statement, ...$args)
    {
        $start = microtime(true);
        $result = parent::query($statement, ...$args);
        $duration = microtime(true) - $start;
        if ($this->monitorEnabled && $this->collector) {
            $this->collector->addQuery([
                'sql' => $statement,
                'bindings' => [],
                'duration' => (int)($duration * 1000),
                'type' => 'query',
            ]);
        }

        return $result;
    }

    public function exec($statement)
    {
        $start = microtime(true);
        $result = parent::exec($statement);
        $duration = microtime(true) - $start;
        if ($this->monitorEnabled && $this->collector) {
            $this->collector->addQuery([
                'sql' => $statement,
                'bindings' => [],
                'duration' => (int)($duration * 1000),
                'type' => 'exec',
            ]);
        }

        return $result;
    }

    public function prepare($statement, $options = [])
    {
        $stmt = parent::prepare($statement, $options);
        if ($stmt && $this->monitorEnabled && $this->collector) {
            $monitoredStmt = new MonitoredPDOStatement($stmt, $statement, $this->collector);

            return $monitoredStmt;
        }

        return $stmt;
    }

    public function setMonitorEnabled($enabled)
    {
        $this->monitorEnabled = $enabled;
    }
}
