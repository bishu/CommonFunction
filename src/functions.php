<?php

declare(strict_types=1);

namespace Bishu\CommonFunction;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;

/**
 * Check whether an IP/port is reachable.
 */
function PortStatus(string $ip, int $port = 80, int $timeout = 1): bool
{
    $socket = @fsockopen($ip, $port, $errno, $errstr, $timeout);

    if ($socket === false) {
        return false;
    }

    fclose($socket);
    return true;
}

/**
 * Build a standard API error payload.
 *
 * @param array<string, mixed> $data Additional top-level response data.
 * @return array<string, mixed>
 */
function ApiError(
    string $message,
    ?string $description = null,
    array $data = [],
    int $statusCode = 0
): array {
    return [
        'statusCode' => $statusCode,
        'errorMessage' => $message,
        'error' => [
            'description' => $description ?: $message,
        ],
    ] + $data;
}

/**
 * Build a standard API success payload.
 *
 * @param array<string, mixed> $data Additional top-level response data.
 * @return array<string, mixed>
 */
function ApiSuccess(string $message, array $data = []): array
{
    return [
        'statusCode' => 1,
        'message' => $message,
    ] + $data;
}

/**
 * Validate an Aadhaar number using the Verhoeff checksum.
 */
function ValidateAadhaar(string $value): string|false
{
    $value = preg_replace('/[\s-]+/', '', trim($value)) ?? '';

    if (preg_match('/^\d{12}$/', $value) !== 1) {
        return false;
    }

    $d = [
        [0,1,2,3,4,5,6,7,8,9],
        [1,2,3,4,0,6,7,8,9,5],
        [2,3,4,0,1,7,8,9,5,6],
        [3,4,0,1,2,8,9,5,6,7],
        [4,0,1,2,3,9,5,6,7,8],
        [5,9,8,7,6,0,4,3,2,1],
        [6,5,9,8,7,1,0,4,3,2],
        [7,6,5,9,8,2,1,0,4,3],
        [8,7,6,5,9,3,2,1,0,4],
        [9,8,7,6,5,4,3,2,1,0],
    ];

    $p = [
        [0,1,2,3,4,5,6,7,8,9],
        [1,5,7,6,2,8,3,0,9,4],
        [5,8,0,3,7,9,6,1,4,2],
        [8,9,1,6,0,4,3,5,2,7],
        [9,4,5,3,1,2,6,8,7,0],
        [4,2,8,6,5,7,3,9,0,1],
        [2,7,9,3,8,0,6,4,1,5],
        [7,0,4,6,9,1,3,2,5,8],
    ];

    $c = 0;
    $digits = array_reverse(array_map('intval', str_split($value)));

    foreach ($digits as $i => $digit) {
        $c = $d[$c][$p[$i % 8][$digit]];
    }

    return $c === 0 ? $value : false;
}

/**
 * Normalize and validate a person's name.
 */
function ValidateName(string $value): string|false
{
    $value = preg_replace('/\s+/u', ' ', trim($value)) ?? '';

    if ($value === '' || preg_match('/^[\p{L} .()]+$/u', $value) !== 1) {
        return false;
    }

    if (substr_count($value, '.') > 2) {
        return false;
    }

    $openBracket = substr_count($value, '(');
    $closeBracket = substr_count($value, ')');

    if ($openBracket !== $closeBracket || $openBracket > 1) {
        return false;
    }

    if ($openBracket === 1 && strpos($value, '(') > strpos($value, ')')) {
        return false;
    }

    if (preg_match('/\(\s*\)/u', $value)) {
        return false;
    }

    if (preg_match('/\p{L}/u', $value) !== 1) {
        return false;
    }

    return $value;
}

/**
 * Normalize and validate an Indian mobile number.
 */
function ValidateMobile(string $value): string|false
{
    $value = preg_replace('/\D+/', '', trim($value)) ?? '';

    if (strlen($value) === 12 && str_starts_with($value, '91')) {
        $value = substr($value, 2);
    }

    return preg_match('/^[5-9]\d{9}$/', $value) === 1 ? $value : false;
}

/**
 * Normalize and validate a PAN number.
 */
function ValidatePan(string $value): string|false
{
    $value = strtoupper(preg_replace('/\s+/', '', $value) ?? '');
    return preg_match('/^[A-Z]{5}[0-9]{4}[A-Z]$/', $value) === 1 ? $value : false;
}

/**
 * Normalize and validate an EPIC number.
 */
function ValidateEpic(string $value): string|false
{
    $value = strtoupper(preg_replace('/\s+/', '', $value) ?? '');
    return preg_match('/^[A-Z0-9]{10}$/', $value) === 1 ? $value : false;
}

function NormalizeAlphaNumeric(string $alphaNumeric): string
{
    return preg_replace('/[^a-zA-Z0-9]/', '', trim($alphaNumeric)) ?? '';
}

/**
 * Normalize a Data URL, raw image binary or plain Base64 into plain Base64.
 * Uses PHPMaker 2026 built-ins for image detection and Data URL handling.
 */
function NormalizeImageBase64(string $imageData): string
{
    if (PhpMaker::call('IsEmpty', $imageData)) {
        return '';
    }

    $binary = null;

    if (PhpMaker::call('StartsString', 'data:', $imageData)) {
        $binary = PhpMaker::call('DataFromBase64Url', $imageData);
        if ($binary === null) {
            return '';
        }
    } else {
        $contentType = PhpMaker::call('ContentType', $imageData);

        if (!PhpMaker::call('IsEmpty', $contentType) && PhpMaker::call('StartsString', 'image/', $contentType)) {
            $binary = $imageData;
        } else {
            $base64 = preg_replace('/\s+/', '', $imageData) ?? '';
            if (PhpMaker::call('IsEmpty', $base64)) {
                return '';
            }

            $decoded = base64_decode($base64, true);
            if ($decoded === false) {
                return '';
            }
            $binary = $decoded;
        }
    }

    if ($binary === null || $binary === '') {
        return '';
    }

    $imageType = PhpMaker::call('ContentType', $binary);
    if (PhpMaker::call('IsEmpty', $imageType) || !PhpMaker::call('StartsString', 'image/', $imageType)) {
        return '';
    }

    return base64_encode($binary);
}

function MaskString(
    mixed $value,
    int $showFirst = 0,
    int $showLast = 4,
    string $mask = '*'
): string {
    if (PhpMaker::call('IsEmpty', $value)) {
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
    $maskLength = $length - $showFirst - $showLast;

    return $first . str_repeat($mask, $maskLength) . $last;
}

/**
 * JSON request wrapper around PHPMaker 2026 ClientUrl().
 */
function JsonClientUrl(
    string $url,
    string|array $data = '',
    string $method = 'GET',
    array $options = []
): mixed {
    $method = strtoupper($method);

    if ($method !== 'GET' && is_array($data)) {
        $data = PhpMaker::call('JsonEncode', $data);
    }

    if ($method !== 'GET') {
        $options['headers'] = array_merge(
            [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            $options['headers'] ?? []
        );
    }

    return PhpMaker::call('ClientUrl', $url, $data, $method, $options);
}

function JsonResponseDecode(string $response): mixed
{
    try {
        return json_decode($response, true, 512, JSON_THROW_ON_ERROR);
    } catch (\JsonException) {
        return null;
    }
}

/**
 * Convert arbitrary API/service data to JSON for logging/storage.
 */
function ApiUsageJson(mixed $data): ?string
{
    if ($data === null) {
        return null;
    }

    if (is_string($data)) {
        return $data;
    }

    try {
        return json_encode(
            $data,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR
        );
    } catch (\Throwable $e) {
        return json_encode(['rawDataError' => $e->getMessage()]);
    }
}

/**
 * Write a named custom error log under PHPMaker's var/log directory.
 */
function LogCustomError(string $name, string $message, array $context = []): void
{
    static $loggers = [];

    $name = strtolower(preg_replace('/[^a-zA-Z0-9_-]/', '', trim($name)) ?? '');
    if ($name === '') {
        $name = 'custom';
    }

    if (!isset($loggers[$name])) {
        $logger = new Logger($name);
        $path = PhpMaker::call('ServerMapPath', "var/log/{$name}.log", true);
        $handler = new StreamHandler($path, Level::Error);
        $formatter = new LineFormatter(
            "[%datetime%] %message% %context%\n",
            'Y-m-d H:i:s',
            true,
            true
        );

        $handler->setFormatter($formatter);
        $logger->pushHandler($handler);
        $loggers[$name] = $logger;
    }

    $loggers[$name]->error($message, $context);
}

/**
 * Save/release the active PHPMaker session lock.
 */
function ReleaseSession(): void
{
    $session = PhpMaker::call('Session');

    if ($session && $session->isStarted()) {
        $session->save();
    }
}

/**
 * True when a payload was produced by ApiSuccess().
 */
function IsApiSuccessResult(mixed $result): bool
{
    return is_array($result) && (int) ($result['statusCode'] ?? 0) === 1;
}

/**
 * Log technical exception details and return a safe ApiError() payload.
 */
function SafeApiException(
    string $logName,
    string $publicMessage,
    string $description,
    \Throwable $e,
    array $context = [],
    int $statusCode = 0
): array {
    LogCustomError(
        $logName,
        $description,
        $context + [
            'Exception' => get_class($e),
            'Message' => $e->getMessage(),
            'File' => $e->getFile(),
            'Line' => $e->getLine(),
        ]
    );

    return ApiError($publicMessage, $description, [], $statusCode);
}

/**
 * Solve a CAPTCHA using 2Captcha from a Base64 image.
 *
 * The API key is read through PHPMaker VaultReveal("CAPTCHA_API_KEY") when
 * available, otherwise from the CAPTCHA_API_KEY environment variable.
 */
function SolveCaptcha(string $base64Image, int $maxTries = 5, int $pollSeconds = 5): string|false
{
    $apiKey = null;

    if (PhpMaker::has('VaultReveal')) {
        $apiKey = PhpMaker::call('VaultReveal', 'CAPTCHA_API_KEY');
    }

    if (!$apiKey) {
        $apiKey = $_ENV['CAPTCHA_API_KEY'] ?? getenv('CAPTCHA_API_KEY') ?: null;
    }

    if (!$apiKey) {
        return false;
    }

    $response = PhpMaker::call(
        'ClientUrl',
        'https://2captcha.com/in.php',
        [
            'key' => $apiKey,
            'method' => 'base64',
            'body' => $base64Image,
            'json' => 1,
        ],
        'POST'
    );

    $json = is_string($response) ? JsonResponseDecode($response) : null;
    if (!is_array($json) || (int) ($json['status'] ?? 0) !== 1) {
        return false;
    }

    $captchaId = (string) ($json['request'] ?? '');
    if ($captchaId === '') {
        return false;
    }

    for ($tries = 0; $tries < max(1, $maxTries); $tries++) {
        sleep(max(1, $pollSeconds));

        $pollResponse = PhpMaker::call(
            'ClientUrl',
            'https://2captcha.com/res.php',
            [
                'key' => $apiKey,
                'action' => 'get',
                'id' => $captchaId,
                'json' => 1,
            ]
        );

        $pollResult = is_string($pollResponse) ? JsonResponseDecode($pollResponse) : null;
        if (is_array($pollResult) && (int) ($pollResult['status'] ?? 0) === 1) {
            return (string) ($pollResult['request'] ?? '');
        }
    }

    return false;
}
