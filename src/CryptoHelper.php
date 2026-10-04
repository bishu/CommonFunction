<?php

declare(strict_types=1);

namespace Bishu\CommonFunction;

final class CryptoHelper
{
    public static function encrypt(string $value, string $key = ''): string
    {
        return (string) PhpMaker::call('Encrypt', $value, $key);
    }

    public static function decrypt(string $value, string $key = ''): string
    {
        return (string) PhpMaker::call('Decrypt', $value, $key);
    }

    public static function urlBase64Encode(string $value): string
    {
        if (PhpMaker::has('UrlBase64Encode')) {
            return (string) PhpMaker::call('UrlBase64Encode', $value);
        }
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    public static function urlBase64Decode(string $value): string|false
    {
        if (PhpMaker::has('UrlBase64Decode')) {
            return PhpMaker::call('UrlBase64Decode', $value);
        }
        return base64_decode(strtr($value, '-_', '+/'), true);
    }

    public static function hash(string $value, string $algorithm = 'sha256'): string
    {
        return hash($algorithm, $value);
    }

    public static function hmac(string $value, string $key, string $algorithm = 'sha256'): string
    {
        return hash_hmac($algorithm, $value, $key);
    }
}
