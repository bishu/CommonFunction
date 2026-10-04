<?php

declare(strict_types=1);

function NormalizeImageBase64(string $imageData): string
{
    if (_CommonFunctionPhpMakerCall("IsEmpty", $imageData)) return "";

    $binary = null;
    if (_CommonFunctionPhpMakerCall("StartsString", "data:", $imageData)) {
        $binary = _CommonFunctionPhpMakerCall("DataFromBase64Url", $imageData);
        if ($binary === null) return "";
    } else {
        $contentType = _CommonFunctionPhpMakerCall("ContentType", $imageData);
        if (!_CommonFunctionPhpMakerCall("IsEmpty", $contentType)
            && _CommonFunctionPhpMakerCall("StartsString", "image/", $contentType)) {
            $binary = $imageData;
        } else {
            $base64 = preg_replace('/\s+/', '', $imageData) ?? "";
            if (_CommonFunctionPhpMakerCall("IsEmpty", $base64)) return "";
            $decoded = base64_decode($base64, true);
            if ($decoded === false) return "";
            $binary = $decoded;
        }
    }

    if ($binary === null || $binary === "") return "";
    $imageType = _CommonFunctionPhpMakerCall("ContentType", $binary);
    if (_CommonFunctionPhpMakerCall("IsEmpty", $imageType)
        || !_CommonFunctionPhpMakerCall("StartsString", "image/", $imageType)) {
        return "";
    }

    return base64_encode($binary);
}
