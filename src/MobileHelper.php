<?php

declare(strict_types=1);

namespace Bishu\CommonFunction;

final class MobileHelper
{
    public static function normalize(string $mobile, string $countryCode = '91'): string
    {
        $mobile = StringHelper::digits($mobile);
        if ($countryCode !== '' && strlen($mobile) === 10 + strlen($countryCode) && str_starts_with($mobile, $countryCode)) {
            $mobile = substr($mobile, strlen($countryCode));
        }
        return $mobile;
    }

    public static function validateIndian(string $mobile): string|false
    {
        $mobile = self::normalize($mobile, '91');
        return preg_match('/^[5-9]\d{9}$/', $mobile) === 1 ? $mobile : false;
    }

    public static function isValidIndian(string $mobile): bool
    {
        return self::validateIndian($mobile) !== false;
    }

    public static function e164(string $mobile, string $countryCode = '91'): string|false
    {
        $mobile = self::normalize($mobile, $countryCode);
        if ($countryCode === '91' && self::validateIndian($mobile) === false) {
            return false;
        }
        if ($mobile === '') {
            return false;
        }
        return '+' . $countryCode . $mobile;
    }

    public static function mask(string $mobile, int $showLast = 4): string
    {
        $mobile = self::normalize($mobile);
        return StringHelper::mask($mobile, 0, $showLast);
    }
}
