<?php

declare(strict_types=1);

function NormalizeAlphaNumeric(string $alphaNumeric): string
{
    return preg_replace('/[^a-zA-Z0-9]/', '', trim($alphaNumeric)) ?? "";
}

function MaskString(
    mixed $value,
    int $showFirst = 0,
    int $showLast = 4,
    string $mask = "*"
): string {
    if (_CommonFunctionPhpMakerCall("IsEmpty", $value)) {
        return "";
    }

    $value = (string)$value;
    $length = mb_strlen($value);
    $showFirst = max(0, $showFirst);
    $showLast = max(0, $showLast);

    if (($showFirst + $showLast) >= $length) {
        return $value;
    }

    $first = $showFirst > 0 ? mb_substr($value, 0, $showFirst) : "";
    $last = $showLast > 0 ? mb_substr($value, -$showLast) : "";
    $maskLength = $length - $showFirst - $showLast;

    return $first . str_repeat($mask, $maskLength) . $last;
}

function NormalizeNullableString(
    mixed $value,
    int $maxLength = 0
): ?string {
    if ($value === null) {
        return null;
    }

    $value = trim((string)$value);

    if ($value === "") {
        return null;
    }

    if ($maxLength > 0 && mb_strlen($value) > $maxLength) {
        $value = mb_substr($value, 0, $maxLength);
    }

    return $value;
}
