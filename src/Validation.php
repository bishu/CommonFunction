<?php

declare(strict_types=1);

function ValidateAadhaar(string $value): string|false
{
    $value = preg_replace('/[\s-]+/', '', trim($value)) ?? "";
    if (preg_match('/^\d{12}$/', $value) !== 1) return false;

    $d = [
        [0,1,2,3,4,5,6,7,8,9],[1,2,3,4,0,6,7,8,9,5],[2,3,4,0,1,7,8,9,5,6],[3,4,0,1,2,8,9,5,6,7],[4,0,1,2,3,9,5,6,7,8],[5,9,8,7,6,0,4,3,2,1],[6,5,9,8,7,1,0,4,3,2],[7,6,5,9,8,2,1,0,4,3],[8,7,6,5,9,3,2,1,0,4],[9,8,7,6,5,4,3,2,1,0]
    ];
    $p = [
        [0,1,2,3,4,5,6,7,8,9],[1,5,7,6,2,8,3,0,9,4],[5,8,0,3,7,9,6,1,4,2],[8,9,1,6,0,4,3,5,2,7],[9,4,5,3,1,2,6,8,7,0],[4,2,8,6,5,7,3,9,0,1],[2,7,9,3,8,0,6,4,1,5],[7,0,4,6,9,1,3,2,5,8]
    ];

    $c = 0;
    $digits = array_reverse(array_map('intval', str_split($value)));
    foreach ($digits as $i => $digit) {
        $c = $d[$c][$p[$i % 8][$digit]];
    }
    return $c === 0 ? $value : false;
}

function ValidateName(string $value): string|false
{
    $value = preg_replace('/\s+/u', ' ', trim($value)) ?? "";
    if ($value === "") return false;
    if (preg_match('/^[\p{L} .()]+$/u', $value) !== 1) return false;
    if (substr_count($value, ".") > 2) return false;

    $openBracket = substr_count($value, "(");
    $closeBracket = substr_count($value, ")");
    if ($openBracket !== $closeBracket || $openBracket > 1) return false;
    if ($openBracket === 1 && strpos($value, "(") > strpos($value, ")")) return false;
    if (preg_match('/\(\s*\)/u', $value)) return false;
    if (preg_match('/\p{L}/u', $value) !== 1) return false;
    return $value;
}

function ValidateMobile(string $value): string|false
{
    $value = preg_replace('/\D+/', '', trim($value)) ?? "";
    if (strlen($value) === 12 && str_starts_with($value, "91")) {
        $value = substr($value, 2);
    }
    return preg_match('/^[5-9]\d{9}$/', $value) === 1 ? $value : false;
}

function ValidatePan(string $value): string|false
{
    $value = strtoupper(preg_replace('/\s+/', '', $value) ?? "");
    return preg_match('/^[A-Z]{5}[0-9]{4}[A-Z]$/', $value) === 1 ? $value : false;
}

function ValidateEpic(string $value): string|false
{
    $value = strtoupper(preg_replace('/\s+/', '', $value) ?? "");
    return preg_match('/^[A-Z0-9]{10}$/', $value) === 1 ? $value : false;
}

function ValidateVehicleRegistration(string $value): string|false
{
    $value = strtoupper(NormalizeAlphaNumeric($value));
    return preg_match('/^[A-Z0-9]{6,15}$/', $value) === 1 ? $value : false;
}
