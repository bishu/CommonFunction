<?php

declare(strict_types=1);

function GenerateUpiQr(float $amount, string $upiId): array
{
    $amount = round($amount, 2);
    if ($amount <= 0) {
        throw new UserException("Invalid Amount|||Payment amount must be greater than zero.");
    }
    if (!extension_loaded("gd")) {
        throw new \RuntimeException("PHP GD extension is required for QR PNG generation.");
    }

    $upiUrl = "upi://pay?" . http_build_query([
        "pa" => $upiId,
        "pn" => "CloudAPI",
        "am" => number_format($amount, 2, ".", ""),
        "cu" => "INR",
        "tn" => "CloudAPI Credit"
    ], "", "&", PHP_QUERY_RFC3986);

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
    if ($png === "") throw new \RuntimeException("Unable to generate UPI QR code.");

    return [
        "upiUrl" => $upiUrl,
        "png" => $png,
        "mimeType" => $result->getMimeType()
    ];
}
