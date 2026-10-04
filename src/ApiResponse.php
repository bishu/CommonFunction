<?php

declare(strict_types=1);

namespace Bishu\CommonFunction;

final class ApiResponse
{
    public static function success(string $message, array $data = [], int $statusCode = 1): array
    {
        return ['statusCode' => $statusCode, 'message' => $message] + $data;
    }

    public static function error(string $message, ?string $description = null, array $data = [], int $statusCode = 0): array
    {
        return [
            'statusCode' => $statusCode,
            'errorMessage' => $message,
            'error' => ['description' => $description ?: $message],
        ] + $data;
    }

    public static function isSuccess(mixed $result): bool
    {
        return is_array($result) && (int) ($result['statusCode'] ?? 0) === 1;
    }

    public static function usageJson(mixed $data): ?string
    {
        if ($data === null) {
            return null;
        }
        if (is_string($data)) {
            return $data;
        }

        try {
            return JsonHelper::encode($data);
        } catch (\Throwable $e) {
            return json_encode(['rawDataError' => $e->getMessage()]) ?: null;
        }
    }

    public static function safeException(string $logName, string $publicMessage, string $description, \Throwable $e, array $context = [], int $statusCode = 0): array
    {
        LogCustomError($logName, $description, $context + [
            'Exception' => get_class($e),
            'Message' => $e->getMessage(),
            'File' => $e->getFile(),
            'Line' => $e->getLine(),
        ]);

        return self::error($publicMessage, $description, [], $statusCode);
    }
}
