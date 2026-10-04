<?php

declare(strict_types=1);

namespace Bishu\CommonFunction;

use JsonException;

final class JsonHelper
{
    public static function encode(mixed $value, int $flags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES): string
    {
        if (PhpMaker::has('JsonEncode')) {
            return (string) PhpMaker::call('JsonEncode', $value, $flags);
        }

        return json_encode($value, $flags | JSON_THROW_ON_ERROR);
    }

    public static function decode(string $json, bool $associative = true): mixed
    {
        return json_decode($json, $associative, 512, JSON_THROW_ON_ERROR);
    }

    public static function tryDecode(string $json, bool $associative = true): mixed
    {
        try {
            return self::decode($json, $associative);
        } catch (JsonException) {
            return null;
        }
    }

    public static function isValid(string $json): bool
    {
        try {
            self::decode($json);
            return true;
        } catch (JsonException) {
            return false;
        }
    }

    public static function pretty(mixed $value): string
    {
        if (is_string($value)) {
            $decoded = self::tryDecode($value);
            if ($decoded !== null) {
                $value = $decoded;
            }
        }

        return json_encode(
            $value,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR
        );
    }

    public static function get(array $data, string $path, mixed $default = null): mixed
    {
        if ($path === '') {
            return $data;
        }

        $value = $data;
        foreach (explode('.', $path) as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }
}
