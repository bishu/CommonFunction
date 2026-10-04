<?php

declare(strict_types=1);

function SolveCaptcha(string $base64Image)
{
    $apiKey = $_ENV["CAPTCHA_API_KEY"] ?? getenv("CAPTCHA_API_KEY") ?: null;
    if (!$apiKey) return false;

    $submitData = [
        'key' => $apiKey,
        'method' => 'base64',
        'body' => $base64Image,
        'json' => 1
    ];

    $response = _CommonFunctionPhpMakerCall("ClientUrl", 'https://2captcha.com/in.php', $submitData, "POST");
    $json = json_decode((string)$response, true);
    if (!$json || ($json['status'] ?? 0) != 1) return false;

    $captchaId = $json['request'];
    $tries = 0;
    $maxTries = 5;
    $pollUrl = 'https://2captcha.com/res.php';

    do {
        sleep(5);
        $pollData = [
            'key' => $apiKey,
            'action' => 'get',
            'id' => $captchaId,
            'json' => 1
        ];

        $pollResponse = _CommonFunctionPhpMakerCall("ClientUrl", $pollUrl, $pollData);
        $pollResult = json_decode((string)$pollResponse, true);
        $tries++;
    } while ((!isset($pollResult['status']) || $pollResult['status'] != 1) && $tries < $maxTries);

    if (!isset($pollResult['status']) || $pollResult['status'] != 1) return false;
    return $pollResult['request'];
}
