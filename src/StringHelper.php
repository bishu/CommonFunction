<?php

declare(strict_types=1);

namespace Bishu\CommonFunction;

final class StringHelper
{
    public static function clean(string $value): string
    {
        return trim(preg_replace('/\s+/u', ' ', $value) ?? '');
    }

    public static function alphaNumeric(string $value): string
    {
        return preg_replace('/[^a-zA-Z0-9]/', '', trim($value)) ?? '';
    }

    public static function digits(string $value): string
    {
        return preg_replace('/\D+/', '', $value) ?? '';
    }

    public static function mask(mixed $value, int $showFirst = 0, int $showLast = 4, string $mask = '*'): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        $value = (string) $value;
        $length = mb_strlen($value);
        $showFirst = max(0, $showFirst);
        $showLast = max(0, $showLast);

        if (($showFirst + $showLast) >= $length) {
            return $value;
        }

        $first = $showFirst > 0 ? mb_substr($value, 0, $showFirst) : '';
        $last = $showLast > 0 ? mb_substr($value, -$showLast) : '';

        return $first . str_repeat($mask, $length - $showFirst - $showLast) . $last;
    }

    public static function truncate(string $value, int $length = 100, string $suffix = '...'): string
    {
        $length = max(0, $length);
        if (mb_strlen($value) <= $length) {
            return $value;
        }

        return mb_substr($value, 0, $length) . $suffix;
    }

    public static function slug(string $value, string $separator = '-'): string
    {
        $value = self::clean($value);
        $value = preg_replace('/[^\pL\pN]+/u', $separator, $value) ?? '';
        $value = trim($value, $separator);

        return mb_strtolower($value);
    }

    public static function startsWith(string $haystack, string $needle): bool
    {
        return PhpMaker::has('StartsString')
            ? (bool) PhpMaker::call('StartsString', $needle, $haystack)
            : str_starts_with($haystack, $needle);
    }

    public static function contains(string $haystack, string $needle): bool
    {
        return PhpMaker::has('ContainsString')
            ? (bool) PhpMaker::call('ContainsString', $haystack, $needle)
            : str_contains($haystack, $needle);
    }

    public static function normalizeImageBase64(string $imageData): string
    {
        if ($imageData === '') {
            return '';
        }

        $binary = null;

        if (self::startsWith($imageData, 'data:')) {
            if (!PhpMaker::has('DataFromBase64Url')) {
                return '';
            }
            $binary = PhpMaker::call('DataFromBase64Url', $imageData);
        } elseif (PhpMaker::has('ContentType')) {
            $contentType = (string) PhpMaker::call('ContentType', $imageData);
            if (self::startsWith($contentType, 'image/')) {
                $binary = $imageData;
            }
        }

        if ($binary === null) {
            $base64 = preg_replace('/\s+/', '', $imageData) ?? '';
            if ($base64 === '') {
                return '';
            }
            $binary = base64_decode($base64, true);
        }

        if (!is_string($binary) || $binary === '') {
            return '';
        }

        if (PhpMaker::has('ContentType')) {
            $imageType = (string) PhpMaker::call('ContentType', $binary);
            if (!self::startsWith($imageType, 'image/')) {
                return '';
            }
        }

        return base64_encode($binary);
    }
}
