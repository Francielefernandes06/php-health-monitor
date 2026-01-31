<?php

declare(strict_types=1);

namespace PHPHealth\Monitor\Tests\Unit;

use PHPHealth\Monitor\Support\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    public function test_get_and_set_config(): void
    {
        $config = new Config([
            'foo' => ['bar' => 'baz'],
        ]);
        $this->assertEquals('baz', $config->get('foo.bar'));
        $config->set('foo.bar', 'qux');
        $this->assertEquals('qux', $config->get('foo.bar'));
    }

    public function test_get_default_value(): void
    {
        $config = new Config();
        $this->assertEquals('default', $config->get('not.exists', 'default'));
    }

    public function test_all_returns_full_config(): void
    {
        $config = new Config(['foo' => 'bar']);
        $all = $config->all();
        $this->assertArrayHasKey('foo', $all);
        $this->assertEquals('bar', $all['foo']);
    }
}
