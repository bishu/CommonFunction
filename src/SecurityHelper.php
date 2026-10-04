<?php

declare(strict_types=1);

namespace Bishu\CommonFunction;

final class SecurityHelper
{
    public static function hashPassword(string $password): string
    {
        return (string) PhpMaker::call('HashPassword', $password);
    }

    public static function verifyPassword(string $hash, string $password): bool
    {
        return (bool) PhpMaker::call('VerifyPassword', $hash, $password);
    }

    public static function token(int $bytes = 32): string
    {
        return bin2hex(random_bytes(max(16, $bytes)));
    }

    public static function randomDigits(int $length = 6): string
    {
        $length = max(1, $length);
        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $result .= (string) random_int(0, 9);
        }
        return $result;
    }

    public static function equals(string $known, string $user): bool
    {
        return hash_equals($known, $user);
    }

    public static function removeXss(array|string|null $value): array|string|null
    {
        if (PhpMaker::has('RemoveXss')) {
            return PhpMaker::call('RemoveXss', $value);
        }
        return $value;
    }
}
