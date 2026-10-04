<?php

declare(strict_types=1);

/**
 * Internal resolver for PHPMaker 2026 generated functions.
 *
 * Public package functions remain global so PHPMaker projects can call:
 *     ValidateMobile($input);
 * without imports or project-specific namespaces.
 */
function _CommonFunctionPhpMakerFunction(string $name): string
{
    static $cache = [];

    $cacheKey = strtolower($name);
    if (isset($cache[$cacheKey])) {
        return $cache[$cacheKey];
    }

    $suffix = '\\' . strtolower($name);
    $matches = [];

    foreach (get_defined_functions()['user'] ?? [] as $function) {
        $normalized = strtolower($function);

        if (!str_starts_with($normalized, 'phpmaker2026\\')) {
            continue;
        }

        if (str_ends_with($normalized, $suffix)) {
            $matches[] = $function;
        }
    }

    if ($matches !== []) {
        return $cache[$cacheKey] = $matches[0];
    }

    throw new \RuntimeException(
        "PHPMaker 2026 function {$name}() is not available."
    );
}

function _CommonFunctionPhpMakerCall(string $name, mixed ...$args): mixed
{
    $function = _CommonFunctionPhpMakerFunction($name);
    return $function(...$args);
}

if (!class_exists('UserException', false)) {
    class UserException extends \RuntimeException
    {
    }
}
