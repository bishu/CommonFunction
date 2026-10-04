<?php

declare(strict_types=1);

namespace Bishu\CommonFunction;

use RuntimeException;

/**
 * Small bridge to PHPMaker 2026 project-level functions.
 *
 * PHPMaker generated functions live inside the project's generated namespace,
 * which changes from project to project. This bridge lets the Composer package
 * reuse those built-in functions without copying PHPMaker's phpfn.php.
 */
final class PhpMaker
{
    private static ?string $namespace = null;

    public static function setNamespace(string $namespace): void
    {
        $namespace = trim($namespace, " \t\n\r\0\x0B\\");

        if ($namespace === '') {
            throw new RuntimeException('PHPMaker namespace cannot be empty.');
        }

        self::$namespace = $namespace;
    }

    public static function namespace(): string
    {
        if (self::$namespace !== null) {
            return self::$namespace;
        }

        $functions = get_defined_functions()['user'] ?? [];
        $candidates = [];

        foreach ($functions as $function) {
            $position = strrpos($function, '\\');
            if ($position === false) {
                continue;
            }

            $name = substr($function, $position + 1);
            if (strcasecmp($name, 'IsEmpty') !== 0) {
                continue;
            }

            $namespace = substr($function, 0, $position);
            if (str_starts_with($namespace, 'PHPMaker2026\\')) {
                $candidates[] = $namespace;
            }
        }

        $candidates = array_values(array_unique($candidates));

        if (count($candidates) === 1) {
            self::$namespace = $candidates[0];
            return self::$namespace;
        }

        if (count($candidates) > 1) {
            throw new RuntimeException(
                'Multiple PHPMaker 2026 namespaces are loaded. Call PhpMaker::setNamespace(__NAMESPACE__) once during application startup.'
            );
        }

        throw new RuntimeException(
            'PHPMaker 2026 namespace was not detected. Call PhpMaker::setNamespace(__NAMESPACE__) from PHPMaker Global Code.'
        );
    }

    public static function functionName(string $function): string
    {
        return self::namespace() . '\\' . ltrim($function, '\\');
    }

    public static function has(string $function): bool
    {
        try {
            return function_exists(self::functionName($function));
        } catch (RuntimeException) {
            return false;
        }
    }

    public static function call(string $function, mixed ...$args): mixed
    {
        $callable = self::functionName($function);

        if (!function_exists($callable)) {
            throw new RuntimeException("PHPMaker function {$callable}() is not available.");
        }

        return $callable(...$args);
    }
}
