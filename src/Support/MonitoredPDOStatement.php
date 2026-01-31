<?php

namespace PHPHealth\Monitor\Support;

use PDOStatement;
use PHPHealth\Monitor\Collectors\DatabaseCollector;

class MonitoredPDOStatement extends PDOStatement
{
    /** @var PDOStatement */
    private $stmt;
    /** @var string */
    private $sql;
    /** @var DatabaseCollector */
    private $collector;

    public function __construct($stmt, $sql, DatabaseCollector $collector)
    {
        $this->stmt = $stmt;
        $this->sql = $sql;
        $this->collector = $collector;
    }

    public function execute($input_parameters = null)
    {
        $start = microtime(true);
        $result = $this->stmt->execute($input_parameters);
        $duration = microtime(true) - $start;
        $sqlWithBindings = $this->interpolateQuery($this->sql, $input_parameters);
        $this->collector->addQuery([
            'sql' => $sqlWithBindings,
            'bindings' => $input_parameters ?? [],
            'duration' => (int)($duration * 1000),
            'type' => 'prepared',
        ]);

        return $result;
    }

    private function interpolateQuery($query, $params)
    {
        if (! $params) {
            return $query;
        }
        $keys = [];
        $values = $params;
        foreach ($params as $key => $value) {
            $keys[] = is_string($key) ? ":$key" : "?";
            $values[$key] = is_numeric($value) ? $value : "'" . addslashes($value) . "'";
        }
        foreach ($keys as $i => $key) {
            $query = preg_replace('/' . preg_quote($key, '/') . '/', $values[$i], $query, 1);
        }

        return $query;
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->stmt, $name], $arguments);
    }
}
