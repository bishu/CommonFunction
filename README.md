# bishu/common-function

Private Composer package containing reusable common functions for PHPMaker 2026 projects.

The package deliberately **does not copy PHPMaker's `phpfn.php`**. Instead, PHPMaker-dependent helpers call the PHPMaker 2026 functions already provided by the generated project through `Bishu\CommonFunction\PhpMaker`.

## Install from the private GitHub repository

Add the repository to the application's `composer.json`:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "git@github.com:bishu/CommonFunction.git"
        }
    ]
}
```

Then install:

```bash
composer require bishu/common-function:dev-main
```

For a tagged release, use the tag instead of `dev-main`.

## PHPMaker namespace

The bridge normally auto-detects the loaded PHPMaker 2026 project namespace. If more than one PHPMaker project namespace is loaded, configure it once from PHPMaker Global Code:

```php
\Bishu\CommonFunction\PhpMaker::setNamespace(__NAMESPACE__);
```

## Import functions

```php
use function Bishu\CommonFunction\ApiError;
use function Bishu\CommonFunction\ApiSuccess;
use function Bishu\CommonFunction\ValidateAadhaar;
use function Bishu\CommonFunction\ValidateMobile;
use function Bishu\CommonFunction\ValidatePan;
use function Bishu\CommonFunction\JsonClientUrl;
use function Bishu\CommonFunction\MaskString;
```

Example:

```php
$mobile = ValidateMobile('+91 98562 99848');

if ($mobile === false) {
    return ApiError('Invalid Mobile', 'Enter a valid mobile number.');
}

return ApiSuccess('Valid mobile.', [
    'mobile' => $mobile,
    'masked' => MaskString($mobile),
]);
```

You can also call any function by its fully-qualified name:

```php
$pan = \Bishu\CommonFunction\ValidatePan($input);
```

## Included functions

### API / JSON

- `ApiError()`
- `ApiSuccess()`
- `ApiUsageJson()`
- `IsApiSuccessResult()`
- `SafeApiException()`
- `JsonClientUrl()`
- `JsonResponseDecode()`

### Validation / normalization

- `ValidateAadhaar()`
- `ValidateName()`
- `ValidateMobile()`
- `ValidatePan()`
- `ValidateEpic()`
- `NormalizeAlphaNumeric()`
- `NormalizeImageBase64()`

### Utility

- `MaskString()`
- `PortStatus()`
- `LogCustomError()`
- `ReleaseSession()`
- `SolveCaptcha()`

## PHPMaker 2026 built-ins used

Depending on the helper called, the package reuses generated PHPMaker functions such as:

- `IsEmpty()`
- `StartsString()`
- `DataFromBase64Url()`
- `ContentType()`
- `JsonEncode()`
- `ClientUrl()`
- `ServerMapPath()`
- `Session()`
- `VaultReveal()`

This keeps PHPMaker's framework implementation in PHPMaker itself and avoids maintaining a copied `phpfn.php` inside this package.

## Project-specific functions

Functions tied to a specific database schema or service should stay in the application/service package rather than this base package. Examples include wallet billing, `serviceusage`, CashSMS server routing, OCR training tables, and application-specific API route tables.
