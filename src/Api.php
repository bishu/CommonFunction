<?php

declare(strict_types=1);

function ApiError(string $message, ?string $description = null, array $data = [], int $statusCode = 0): array
{
    return [
        "statusCode" => $statusCode,
        "errorMessage" => $message,
        "error" => ["description" => $description ?: $message],
    ] + $data;
}

function ApiSuccess(string $message, array $data = []): array
{
    return ["statusCode" => 1, "message" => $message] + $data;
}

function JsonClientUrl(string $url, string|array $data = "", string $method = "GET", array $options = []): mixed
{
    $method = strtoupper($method);
    if ($method !== "GET" && is_array($data)) {
        $data = _CommonFunctionPhpMakerCall("JsonEncode", $data);
    }
    if ($method !== "GET") {
        $options["headers"] = array_merge([
            "Accept" => "application/json",
            "Content-Type" => "application/json",
        ], $options["headers"] ?? []);
    }
    return _CommonFunctionPhpMakerCall("ClientUrl", $url, $data, $method, $options);
}

function JsonResponseDecode(string $response): mixed
{
    try {
        return json_decode($response, true, 512, JSON_THROW_ON_ERROR);
    } catch (\JsonException) {
        return null;
    }
}
