<?php

declare(strict_types=1);

namespace Bishu\CommonFunction;

use DateTimeImmutable;
use DateTimeInterface;
use Throwable;

final class DateHelper
{
    public static function currentDate(int $namedFormat = -1): string
    {
        return (string) PhpMaker::call('CurrentDate', $namedFormat);
    }

    public static function currentDateTime(int $namedFormat = -1): string
    {
        return (string) PhpMaker::call('CurrentDateTime', $namedFormat);
    }

    public static function diff(string $from, string $to, string $interval = 'd'): int|false
    {
        return PhpMaker::call('DateDiff', $from, $to, $interval);
    }

    public static function format(string|DateTimeInterface $value, string $format = 'Y-m-d H:i:s'): string|false
    {
        try {
            $date = $value instanceof DateTimeInterface
                ? DateTimeImmutable::createFromInterface($value)
                : new DateTimeImmutable($value);
            return $date->format($format);
        } catch (Throwable) {
            return false;
        }
    }

    public static function isExpired(string $dateTime, ?string $now = null): bool
    {
        try {
            $target = new DateTimeImmutable($dateTime);
            $current = new DateTimeImmutable($now ?? self::currentDateTime());
            return $target < $current;
        } catch (Throwable) {
            return true;
        }
    }

    public static function age(string $birthDate, ?string $asOf = null): int|false
    {
        try {
            $birth = new DateTimeImmutable($birthDate);
            $date = new DateTimeImmutable($asOf ?? self::currentDate());
            if ($birth > $date) {
                return false;
            }
            return $birth->diff($date)->y;
        } catch (Throwable) {
            return false;
        }
    }
}
