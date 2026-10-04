<?php

declare(strict_types=1);

namespace Bishu\CommonFunction;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;

// Backward-compatible function API. New code should prefer the helper classes.

function PortStatus(string $ip, int $port = 80, int $timeout = 1): bool
{
    return HttpClient::portStatus($ip, $port, $timeout);
}

function ApiError(string $message, ?string $description = null, array $data = [], int $statusCode = 0): array
{
    return ApiResponse::error($message, $description, $data, $statusCode);
}

function ApiSuccess(string $message, array $data = []): array
{
    return ApiResponse::success($message, $data);
}

function ValidateAadhaar(string $value): string|false
{
    return ValidationHelper::aadhaar($value);
}

function ValidateName(string $value): string|false
{
    return ValidationHelper::name($value);
}

function ValidateMobile(string $value): string|false
{
    return ValidationHelper::mobile($value);
}

function ValidatePan(string $value): string|false
{
    return ValidationHelper::pan($value);
}

function ValidateEpic(string $value): string|false
{
    return ValidationHelper::epic($value);
}

function NormalizeAlphaNumeric(string $value): string
{
    return StringHelper::alphaNumeric($value);
}

function NormalizeImageBase64(string $imageData): string
{
    return StringHelper::normalizeImageBase64($imageData);
}

function MaskString(mixed $value, int $showFirst = 0, int $showLast = 4, string $mask = '*'): string
{
    return StringHelper::mask($value, $showFirst, $showLast, $mask);
}

function JsonClientUrl(string $url, string|array $data = '', string $method = 'GET', array $options = []): mixed
{
    return HttpClient::json($url, $data, $method, $options);
}

function JsonResponseDecode(string $response): mixed
{
    return JsonHelper::tryDecode($response);
}

function ApiUsageJson(mixed $data): ?string
{
    return ApiResponse::usageJson($data);
}

function IsApiSuccessResult(mixed $result): bool
{
    return ApiResponse::isSuccess($result);
}

function SafeApiException(
    string $logName,
    string $publicMessage,
    string $description,
    \Throwable $e,
    array $context = [],
    int $statusCode = 0
): array {
    return ApiResponse::safeException($logName, $publicMessage, $description, $e, $context, $statusCode);
}

/** Write a named custom error log under PHPMaker's var/log directory. */
function LogCustomError(string $name, string $message, array $context = []): void
{
    static $loggers = [];

    $name = strtolower(preg_replace('/[^a-zA-Z0-9_-]/', '', trim($name)) ?? '');
    $name = $name !== '' ? $name : 'custom';

    if (!isset($loggers[$name])) {
        $logger = new Logger($name);
        $path = PhpMaker::call('ServerMapPath', "var/log/{$name}.log", true);
        $handler = new StreamHandler($path, Level::Error);
        $handler->setFormatter(new LineFormatter(
            "[%datetime%] %message% %context%\n",
            'Y-m-d H:i:s',
            true,
            true
        ));
        $logger->pushHandler($handler);
        $loggers[$name] = $logger;
    }

    $loggers[$name]->error($message, $context);
}

/** Save/release the active PHPMaker session lock. */
function ReleaseSession(): void
{
    $session = PhpMaker::call('Session');
    if ($session && $session->isStarted()) {
        $session->save();
    }
}

/** Solve a CAPTCHA using 2Captcha from a Base64 image. */
function SolveCaptcha(string $base64Image, int $maxTries = 5, int $pollSeconds = 5): string|false
{
    $apiKey = PhpMaker::has('VaultReveal')
        ? PhpMaker::call('VaultReveal', 'CAPTCHA_API_KEY')
        : ($_ENV['CAPTCHA_API_KEY'] ?? getenv('CAPTCHA_API_KEY') ?: null);

    if (!$apiKey) {
        return false;
    }

    $response = HttpClient::post('https://2captcha.com/in.php', [
        'key' => $apiKey,
        'method' => 'base64',
        'body' => $base64Image,
        'json' => 1,
    ]);

    $json = is_string($response) ? JsonHelper::tryDecode($response) : null;
    if (!is_array($json) || (int) ($json['status'] ?? 0) !== 1) {
        return false;
    }

    $captchaId = (string) ($json['request'] ?? '');
    if ($captchaId === '') {
        return false;
    }

    for ($tries = 0; $tries < max(1, $maxTries); $tries++) {
        sleep(max(1, $pollSeconds));
        $pollResponse = HttpClient::get('https://2captcha.com/res.php', [
            'key' => $apiKey,
            'action' => 'get',
            'id' => $captchaId,
            'json' => 1,
        ]);
        $poll = is_string($pollResponse) ? JsonHelper::tryDecode($pollResponse) : null;
        if (is_array($poll) && (int) ($poll['status'] ?? 0) === 1) {
            return (string) ($poll['request'] ?? '');
        }
    }

    return false;
}
