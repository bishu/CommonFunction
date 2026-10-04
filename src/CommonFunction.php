<?php

declare(strict_types=1);

/**
 * Internal resolver for PHPMaker 2026 generated functions.
 *
 * Public package functions remain global so PHPMaker projects can call:
 *     ValidateMobile($input);
 * without imports or project-specific namespaces.
 */
function _CommonFunctionPhpMakerFunction(string $name): string
{
    static $cache = [];

    if (isset($cache[$name])) {
        return $cache[$name];
    }

    $suffix = '\\' . $name;
    $matches = [];

    foreach (get_defined_functions()['user'] ?? [] as $function) {
        if (!str_starts_with($function, 'phpmaker2026\\')) {
            continue;
        }

        if (str_ends_with($function, strtolower($suffix))) {
            $matches[] = $function;
        }
    }

    if (count($matches) === 1) {
        return $cache[$name] = $matches[0];
    }

    if (count($matches) > 1) {
        return $cache[$name] = $matches[0];
    }

    throw new \RuntimeException(
        "PHPMaker 2026 function {$name}() is not available."
    );
}

function _CommonFunctionPhpMakerCall(string $name, mixed ...$args): mixed
{
    $function = _CommonFunctionPhpMakerFunction($name);
    return $function(...$args);
}

if (!class_exists('UserException', false)) {
    class UserException extends \RuntimeException
    {
    }
}

/**
 * Check whether an IP/port is reachable.
 */
function PortStatus(
    string $ip,
    int $port = 80,
    int $timeout = 1
): bool {

    $socket = @fsockopen(
        $ip,
        $port,
        $errno,
        $errstr,
        $timeout
    );

    if ($socket === false) {
        return false;
    }

    fclose($socket);

    return true;
}

/**
 * Build a standard API error payload.
 *
 * @param array<string, mixed> $data Additional top-level response data
 * @return array<string, mixed>
 */
function ApiError(
    string $message,
    ?string $description = null,
    array $data = [],
    int $statusCode = 0
): array {
    return [
        "statusCode" => $statusCode,
        "errorMessage" => $message,
        "error" => [
            "description" => $description ?: $message,
        ],
    ] + $data;
}

/**
 * Build a standard API success payload.
 *
 * @param array<string, mixed> $data Additional top-level response data
 * @return array<string, mixed>
 */
function ApiSuccess(
    string $message,
    array $data = []
): array {
    return [
        "statusCode" => 1,
        "message" => $message,
    ] + $data;
}

function ValidateAadhaar(string $value): string|false
{
    $value = preg_replace('/[\s-]+/', '', trim($value)) ?? "";

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
        [9,8,7,6,5,4,3,2,1,0]
    ];

    $p = [
        [0,1,2,3,4,5,6,7,8,9],
        [1,5,7,6,2,8,3,0,9,4],
        [5,8,0,3,7,9,6,1,4,2],
        [8,9,1,6,0,4,3,5,2,7],
        [9,4,5,3,1,2,6,8,7,0],
        [4,2,8,6,5,7,3,9,0,1],
        [2,7,9,3,8,0,6,4,1,5],
        [7,0,4,6,9,1,3,2,5,8]
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

    if ($value === "") {
        return false;
    }

    if (preg_match('/^[\p{L} .()]+$/u', $value) !== 1) {
        return false;
    }

    if (substr_count($value, ".") > 2) {
        return false;
    }

    $openBracket = substr_count($value, "(");
    $closeBracket = substr_count($value, ")");

    if ($openBracket !== $closeBracket || $openBracket > 1) {
        return false;
    }

    if ($openBracket === 1 && strpos($value, "(") > strpos($value, ")")) {
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

function ValidateMobile(string $value): string|false
{
    $value = preg_replace('/\D+/', '', trim($value)) ?? "";

    if (strlen($value) === 12 && str_starts_with($value, "91")) {
        $value = substr($value, 2);
    }

    if (preg_match('/^[5-9]\d{9}$/', $value) !== 1) {
        return false;
    }

    return $value;
}

function ValidatePan(string $value): string|false
{
    $value = strtoupper(preg_replace('/\s+/', '', $value) ?? "");

    return preg_match('/^[A-Z]{5}[0-9]{4}[A-Z]$/', $value) === 1
        ? $value
        : false;
}

function ValidateEpic(string $value): string|false
{
    $value = strtoupper(preg_replace('/\s+/', '', $value) ?? "");

    return preg_match('/^[A-Z0-9]{10}$/', $value) === 1
        ? $value
        : false;
}

function ValidateVehicleRegistration(string $value): string|false
{
    $value = strtoupper(NormalizeAlphaNumeric($value));

    return preg_match('/^[A-Z0-9]{6,15}$/', $value) === 1
        ? $value
        : false;
}

function NormalizeAlphaNumeric(string $alphaNumeric): string
{
    return preg_replace('/[^a-zA-Z0-9]/', '', trim($alphaNumeric)) ?? "";
}

function NormalizeImageBase64(string $imageData): string
{
    if (_CommonFunctionPhpMakerCall("IsEmpty", $imageData)) {
        return "";
    }

    $binary = null;

    if (_CommonFunctionPhpMakerCall("StartsString", "data:", $imageData)) {
        $binary = _CommonFunctionPhpMakerCall("DataFromBase64Url", $imageData);

        if ($binary === null) {
            return "";
        }
    } else {
        $contentType = _CommonFunctionPhpMakerCall("ContentType", $imageData);

        if (
            !_CommonFunctionPhpMakerCall("IsEmpty", $contentType)
            && _CommonFunctionPhpMakerCall("StartsString", "image/", $contentType)
        ) {
            $binary = $imageData;
        } else {
            $base64 = preg_replace('/\s+/', '', $imageData) ?? "";

            if (_CommonFunctionPhpMakerCall("IsEmpty", $base64)) {
                return "";
            }

            $decoded = base64_decode($base64, true);

            if ($decoded === false) {
                return "";
            }

            $binary = $decoded;
        }
    }

    if ($binary === null || $binary === "") {
        return "";
    }

    $imageType = _CommonFunctionPhpMakerCall("ContentType", $binary);

    if (
        _CommonFunctionPhpMakerCall("IsEmpty", $imageType)
        || !_CommonFunctionPhpMakerCall("StartsString", "image/", $imageType)
    ) {
        return "";
    }

    return base64_encode($binary);
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

/**
 * Trim a value, convert an empty string to NULL, and optionally
 * limit the result using multibyte-safe string functions.
 */
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

function JsonClientUrl(
    string $url,
    string|array $data = "",
    string $method = "GET",
    array $options = []
): mixed {
    $method = strtoupper($method);

    if ($method !== "GET" && is_array($data)) {
        $data = _CommonFunctionPhpMakerCall("JsonEncode", $data);
    }

    if ($method !== "GET") {
        $options["headers"] = array_merge(
            [
                "Accept" => "application/json",
                "Content-Type" => "application/json",
            ],
            $options["headers"] ?? []
        );
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

function LogCustomError(
    string $name,
    string $message,
    array $context = []
): void {
    static $loggers = [];

    $name = strtolower(preg_replace('/[^a-zA-Z0-9_-]/', '', trim($name)) ?? "");

    if ($name === "") {
        $name = "custom";
    }

    if (!isset($loggers[$name])) {
        $logger = new \Monolog\Logger($name);

        $handler = new \Monolog\Handler\StreamHandler(
            _CommonFunctionPhpMakerCall("ServerMapPath", "var/log/{$name}.log", true),
            \Monolog\Level::Error
        );

        $formatter = new \Monolog\Formatter\LineFormatter(
            "[%datetime%] %message% %context%\n",
            "Y-m-d H:i:s",
            true,
            true
        );

        $handler->setFormatter($formatter);
        $logger->pushHandler($handler);
        $loggers[$name] = $logger;
    }

    $loggers[$name]->error($message, $context);
}

function ReleaseSession(): void
{
    $session = _CommonFunctionPhpMakerCall("Session");

    if ($session && $session->isStarted()) {
        $session->save();
    }
}

/**
 * Solve a CAPTCHA using 2Captcha from a base64 image.
 */
function SolveCaptcha(string $base64Image)
{
    $apiKey = $_ENV["CAPTCHA_API_KEY"] ?? getenv("CAPTCHA_API_KEY") ?: null;

    if (!$apiKey) {
        return false;
    }

    $submitData = [
        'key' => $apiKey,
        'method' => 'base64',
        'body' => $base64Image,
        'json' => 1
    ];

    $response = _CommonFunctionPhpMakerCall(
        "ClientUrl",
        'https://2captcha.com/in.php',
        $submitData,
        "POST"
    );
    $json = json_decode((string)$response, true);

    if (!$json || ($json['status'] ?? 0) != 1) {
        return false;
    }

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

    if (!isset($pollResult['status']) || $pollResult['status'] != 1) {
        return false;
    }

    return $pollResult['request'];
}

function GenerateUpiQr(
    float $amount,
    string $upiId
): array {
    $amount = round($amount, 2);

    if ($amount <= 0) {
        throw new UserException(
            "Invalid Amount|||Payment amount must be greater than zero."
        );
    }

    if (!extension_loaded("gd")) {
        throw new \RuntimeException(
            "PHP GD extension is required for QR PNG generation."
        );
    }

    $upiUrl = "upi://pay?" . http_build_query(
        [
            "pa" => $upiId,
            "pn" => "CloudAPI",
            "am" => number_format($amount, 2, ".", ""),
            "cu" => "INR",
            "tn" => "CloudAPI Credit"
        ],
        "",
        "&",
        PHP_QUERY_RFC3986
    );

    $qrCode = new \Endroid\QrCode\QrCode(
        data: $upiUrl,
        encoding: new \Endroid\QrCode\Encoding\Encoding("UTF-8"),
        errorCorrectionLevel: \Endroid\QrCode\ErrorCorrectionLevel::High,
        size: 300,
        margin: 10,
        roundBlockSizeMode: \Endroid\QrCode\RoundBlockSizeMode::Margin,
        foregroundColor: new \Endroid\QrCode\Color\Color(0, 0, 0),
        backgroundColor: new \Endroid\QrCode\Color\Color(255, 255, 255)
    );

    $writer = new \Endroid\QrCode\Writer\PngWriter();
    $result = $writer->write($qrCode);
    $png = $result->getString();

    if ($png === "") {
        throw new \RuntimeException("Unable to generate UPI QR code.");
    }

    return [
        "upiUrl" => $upiUrl,
        "png" => $png,
        "mimeType" => $result->getMimeType()
    ];
}

/** Extract an input value by matching its id or name. */
function HtmlExtractInputValue(string $html, string $field): ?string
{
    if (!preg_match_all('~<input\b[^>]*>~is', $html, $matches)) {
        return null;
    }

    foreach ($matches[0] as $tag) {
        $id = HtmlExtractAttribute($tag, "id");
        $name = HtmlExtractAttribute($tag, "name");

        if (
            ($id !== null && strcasecmp($id, $field) === 0)
            || ($name !== null && strcasecmp($name, $field) === 0)
        ) {
            return HtmlExtractAttribute($tag, "value");
        }
    }

    return null;
}

function HtmlExtractAttribute(string $tag, string $attribute): ?string
{
    $attribute = preg_quote($attribute, "~");
    $pattern = '~\b' . $attribute . '\s*=\s*' . '(?:"([^"]*)"|\'([^\']*)\'|([^\s>]+))~is';

    if (!preg_match($pattern, $tag, $match)) {
        return null;
    }

    foreach ([1, 2, 3] as $index) {
        if (array_key_exists($index, $match)) {
            return CleanHtmlText($match[$index]);
        }
    }

    return null;
}

function HtmlExtractStyledTagText(
    string $html,
    string $tagName,
    string $styleNeedle
): ?string {
    $tagName = preg_quote($tagName, "~");
    $pattern = '~<' . $tagName . '\b([^>]*)>(.*?)</' . $tagName . '>~is';

    if (!preg_match_all($pattern, $html, $matches, PREG_SET_ORDER)) {
        return null;
    }

    $needle = NormalizeHtmlStyle($styleNeedle);

    foreach ($matches as $match) {
        $style = HtmlExtractAttribute('<x ' . $match[1] . '>', "style");

        if ($style !== null && str_contains(NormalizeHtmlStyle($style), $needle)) {
            return CleanHtmlText($match[2]);
        }
    }

    return null;
}

function HtmlExtractStyledTextAnyTag(string $html, string $styleNeedle): ?string
{
    $pattern = '~<([a-z][a-z0-9]*)\b([^>]*\bstyle\s*=\s*(?:"[^"]*"|\'[^\']*\')[^>]*)>(.*?)</\1>~is';

    if (!preg_match_all($pattern, $html, $matches, PREG_SET_ORDER)) {
        return null;
    }

    $needle = NormalizeHtmlStyle($styleNeedle);

    foreach ($matches as $match) {
        $style = HtmlExtractAttribute('<x ' . $match[2] . '>', "style");

        if ($style !== null && str_contains(NormalizeHtmlStyle($style), $needle)) {
            return CleanHtmlText($match[3]);
        }
    }

    return null;
}

function HtmlExtractTagText(string $html, string $tagName): ?string
{
    $tagName = preg_quote($tagName, "~");

    if (preg_match('~<' . $tagName . '\b[^>]*>(.*?)</' . $tagName . '>~is', $html, $match)) {
        return CleanHtmlText($match[1]);
    }

    return null;
}

function NormalizeHtmlStyle(string $value): string
{
    return strtolower(preg_replace('/\s+/', '', $value) ?? $value);
}

function CleanHtmlText(?string $value): ?string
{
    if ($value === null) {
        return null;
    }

    $value = strip_tags($value);
    $value = _CommonFunctionPhpMakerCall(
        "ConvertToString",
        _CommonFunctionPhpMakerCall("HtmlDecode", $value)
    );

    $cleaned = preg_replace('/\s+/u', ' ', $value);

    if ($cleaned !== null) {
        $value = $cleaned;
    }

    $value = trim($value);

    return _CommonFunctionPhpMakerCall("IsEmpty", $value)
        ? null
        : $value;
}
