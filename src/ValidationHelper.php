<?php

declare(strict_types=1);

namespace Bishu\CommonFunction;

final class ValidationHelper
{
    public static function aadhaar(string $value): string|false
    {
        $value = preg_replace('/[\s-]+/', '', trim($value)) ?? '';
        if (preg_match('/^\d{12}$/', $value) !== 1) {
            return false;
        }

        $d = [[0,1,2,3,4,5,6,7,8,9],[1,2,3,4,0,6,7,8,9,5],[2,3,4,0,1,7,8,9,5,6],[3,4,0,1,2,8,9,5,6,7],[4,0,1,2,3,9,5,6,7,8],[5,9,8,7,6,0,4,3,2,1],[6,5,9,8,7,1,0,4,3,2],[7,6,5,9,8,2,1,0,4,3],[8,7,6,5,9,3,2,1,0,4],[9,8,7,6,5,4,3,2,1,0]];
        $p = [[0,1,2,3,4,5,6,7,8,9],[1,5,7,6,2,8,3,0,9,4],[5,8,0,3,7,9,6,1,4,2],[8,9,1,6,0,4,3,5,2,7],[9,4,5,3,1,2,6,8,7,0],[4,2,8,6,5,7,3,9,0,1],[2,7,9,3,8,0,6,4,1,5],[7,0,4,6,9,1,3,2,5,8]];

        $c = 0;
        foreach (array_reverse(array_map('intval', str_split($value))) as $i => $digit) {
            $c = $d[$c][$p[$i % 8][$digit]];
        }
        return $c === 0 ? $value : false;
    }

    public static function name(string $value): string|false
    {
        $value = StringHelper::clean($value);
        if ($value === '' || preg_match('/^[\p{L} .()]+$/u', $value) !== 1) {
            return false;
        }
        if (substr_count($value, '.') > 2) {
            return false;
        }
        $open = substr_count($value, '(');
        $close = substr_count($value, ')');
        if ($open !== $close || $open > 1) {
            return false;
        }
        if ($open === 1 && strpos($value, '(') > strpos($value, ')')) {
            return false;
        }
        if (preg_match('/\(\s*\)/u', $value) === 1 || preg_match('/\p{L}/u', $value) !== 1) {
            return false;
        }
        return $value;
    }

    public static function mobile(string $value): string|false
    {
        return MobileHelper::validateIndian($value);
    }

    public static function pan(string $value): string|false
    {
        $value = strtoupper(preg_replace('/\s+/', '', $value) ?? '');
        return preg_match('/^[A-Z]{5}[0-9]{4}[A-Z]$/', $value) === 1 ? $value : false;
    }

    public static function epic(string $value): string|false
    {
        $value = strtoupper(preg_replace('/\s+/', '', $value) ?? '');
        return preg_match('/^[A-Z0-9]{10}$/', $value) === 1 ? $value : false;
    }

    public static function email(string $value): string|false
    {
        $value = trim($value);
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false ? $value : false;
    }

    public static function url(string $value): string|false
    {
        $value = trim($value);
        return filter_var($value, FILTER_VALIDATE_URL) !== false ? $value : false;
    }

    public static function required(mixed $value): bool
    {
        if (PhpMaker::has('IsEmpty')) {
            return !(bool) PhpMaker::call('IsEmpty', $value);
        }
        return !($value === null || $value === '' || $value === []);
    }
}
