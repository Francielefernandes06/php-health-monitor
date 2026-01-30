<?php

declare(strict_types=1);

namespace PHPHealth\Monitor\Collectors;

use PHPHealth\Monitor\Contracts\CollectorInterface;
use PHPHealth\Monitor\Support\Config;

/**
 * Coletor de erros e exceções
 */
class ErrorCollector implements CollectorInterface
{
    private Config $config;
    private bool $started = false;

    /** @var array<int, array<string, mixed>> */
    private array $errors = [];

    /** @var callable|null */
    private $previousErrorHandler = null;

    /** @var callable|null */
    private $previousExceptionHandler = null;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function start(): void
    {
        if ($this->started) {
            return;
        }

        $this->started = true;

        // Registra error handler
        $this->previousErrorHandler = set_error_handler([$this, 'handleError']);

        // Registra exception handler
        $this->previousExceptionHandler = set_exception_handler([$this, 'handleException']);
    }

    public function stop(): void
    {
        if (!$this->started) {
            return;
        }

        $this->started = false;

        // Restaura handlers anteriores
        if ($this->previousErrorHandler !== null) {
            set_error_handler($this->previousErrorHandler);
        }

        if ($this->previousExceptionHandler !== null) {
            set_exception_handler($this->previousExceptionHandler);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function collect(): array
    {
        return [
            'total_errors' => count($this->errors),
            'errors' => $this->errors,
        ];
    }

    public function reset(): void
    {
        $this->errors = [];
    }

    /**
     * Handler de erros PHP
     */
    public function handleError(int $errno, string $errstr, string $errfile, int $errline): bool
    {
        if (!$this->started) {
            return false;
        }

        $this->errors[] = [
            'type' => 'error',
            'level' => $errno,
            'level_name' => $this->getErrorLevelName($errno),
            'message' => $errstr,
            'file' => $errfile,
            'line' => $errline,
            'timestamp' => time(),
        ];

        // Chama o handler anterior se existir
        if ($this->previousErrorHandler !== null) {
            return call_user_func($this->previousErrorHandler, $errno, $errstr, $errfile, $errline);
        }

        return false;
    }

    /**
     * Handler de exceções
     */
    public function handleException(\Throwable $exception): void
    {
        if (!$this->started) {
            return;
        }

        $this->errors[] = [
            'type' => 'exception',
            'class' => get_class($exception),
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'timestamp' => time(),
        ];

        // Chama o handler anterior se existir
        if ($this->previousExceptionHandler !== null) {
            call_user_func($this->previousExceptionHandler, $exception);
        }
    }

    /**
     * Obtém o nome do nível de erro
     */
    private function getErrorLevelName(int $level): string
    {
        return match ($level) {
            E_ERROR => 'E_ERROR',
            E_WARNING => 'E_WARNING',
            E_PARSE => 'E_PARSE',
            E_NOTICE => 'E_NOTICE',
            E_CORE_ERROR => 'E_CORE_ERROR',
            E_CORE_WARNING => 'E_CORE_WARNING',
            E_COMPILE_ERROR => 'E_COMPILE_ERROR',
            E_COMPILE_WARNING => 'E_COMPILE_WARNING',
            E_USER_ERROR => 'E_USER_ERROR',
            E_USER_WARNING => 'E_USER_WARNING',
            E_USER_NOTICE => 'E_USER_NOTICE',
            E_STRICT => 'E_STRICT',
            E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
            E_DEPRECATED => 'E_DEPRECATED',
            E_USER_DEPRECATED => 'E_USER_DEPRECATED',
            default => 'UNKNOWN',
        };
    }
}
