# bishu/common-function

Private Composer package of reusable helpers for PHPMaker 2026 projects.

The package deliberately **does not copy PHPMaker's `phpfn.php`**. PHPMaker-specific modules call the generated PHPMaker 2026 functions through `Bishu\CommonFunction\PhpMaker`.

## Install

Add the private repository to the application's `composer.json`:

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

From PHPMaker Global Code, configure the generated project namespace once:

```php
\Bishu\CommonFunction\PhpMaker::setNamespace(__NAMESPACE__);
```

## Core modules

### 1. StringHelper

```php
use Bishu\CommonFunction\StringHelper;

$value = StringHelper::clean('  Hello   World  ');
$digits = StringHelper::digits('ID: 123-456');
$masked = StringHelper::mask('9856299848', 0, 4);
$slug = StringHelper::slug('My Common Function');
```

Methods include `clean()`, `alphaNumeric()`, `digits()`, `mask()`, `truncate()`, `slug()`, `startsWith()`, `contains()` and `normalizeImageBase64()`.

### 2. JsonHelper

```php
use Bishu\CommonFunction\JsonHelper;

$json = JsonHelper::encode(['status' => true]);
$data = JsonHelper::tryDecode($json);
$value = JsonHelper::get($data, 'user.mobile');
```

Methods include `encode()`, `decode()`, `tryDecode()`, `isValid()`, `pretty()` and `get()`.

### 3. ValidationHelper

```php
use Bishu\CommonFunction\ValidationHelper;

$mobile = ValidationHelper::mobile('+91 98562 99848');
$pan = ValidationHelper::pan('ABCDE1234F');
$aadhaar = ValidationHelper::aadhaar($input);
```

Methods include `aadhaar()`, `name()`, `mobile()`, `pan()`, `epic()`, `email()`, `url()` and `required()`.

### 4. CryptoHelper

Uses PHPMaker 2026 `Encrypt()`, `Decrypt()`, `UrlBase64Encode()` and `UrlBase64Decode()` where available.

```php
use Bishu\CommonFunction\CryptoHelper;

$encrypted = CryptoHelper::encrypt($value);
$plain = CryptoHelper::decrypt($encrypted);
$hash = CryptoHelper::hash($value);
```

### 5. SecurityHelper

Uses PHPMaker 2026 password hashing and XSS helpers.

```php
use Bishu\CommonFunction\SecurityHelper;

$hash = SecurityHelper::hashPassword($password);
$valid = SecurityHelper::verifyPassword($hash, $password);
$token = SecurityHelper::token();
$otp = SecurityHelper::randomDigits(6);
```

### 6. HttpClient

Wraps PHPMaker 2026 `ClientUrl()`.

```php
use Bishu\CommonFunction\HttpClient;

$response = HttpClient::get($url, ['id' => 10]);
$response = HttpClient::post($url, $data);
$response = HttpClient::json($url, $data, 'POST');
$data = HttpClient::jsonDecode($url, [], 'GET');
```

### 7. ApiResponse

```php
use Bishu\CommonFunction\ApiResponse;

return ApiResponse::success('Record found.', [
    'data' => $row,
]);

return ApiResponse::error(
    'Record not found.',
    'No matching record was found.'
);
```

Methods include `success()`, `error()`, `isSuccess()`, `usageJson()` and `safeException()`.

### 8. DateHelper

Uses PHPMaker 2026 `CurrentDate()`, `CurrentDateTime()` and `DateDiff()`.

```php
use Bishu\CommonFunction\DateHelper;

$today = DateHelper::currentDate();
$now = DateHelper::currentDateTime();
$days = DateHelper::diff('2026-01-01', '2026-01-10');
$age = DateHelper::age('1990-01-01');
```

### 9. MobileHelper

```php
use Bishu\CommonFunction\MobileHelper;

$mobile = MobileHelper::normalize('+91 98562 99848');
$valid = MobileHelper::isValidIndian($mobile);
$e164 = MobileHelper::e164($mobile);
$masked = MobileHelper::mask($mobile);
```

### 10. DatabaseHelper

Uses PHPMaker 2026/Doctrine database functions and connection objects.

```php
use Bishu\CommonFunction\DatabaseHelper;

$count = DatabaseHelper::scalar('SELECT COUNT(*) FROM users');
$row = DatabaseHelper::row('SELECT * FROM users LIMIT 1');
$rows = DatabaseHelper::rows('SELECT * FROM users');

$user = DatabaseHelper::fetchAssociative(
    'SELECT * FROM users WHERE UserID = :id',
    ['id' => 1005]
);
```

For new code, parameterized Doctrine calls such as `fetchAssociative()` and `execute()` are preferred when input values are dynamic.

## Backward-compatible functions

Existing projects can continue using the original function API because `src/functions.php` now delegates to the modules:

- `ApiError()`
- `ApiSuccess()`
- `ValidateAadhaar()`
- `ValidateName()`
- `ValidateMobile()`
- `ValidatePan()`
- `ValidateEpic()`
- `NormalizeAlphaNumeric()`
- `NormalizeImageBase64()`
- `MaskString()`
- `JsonClientUrl()`
- `JsonResponseDecode()`
- `ApiUsageJson()`
- `IsApiSuccessResult()`
- `SafeApiException()`
- `PortStatus()`
- `LogCustomError()`
- `ReleaseSession()`
- `SolveCaptcha()`

New application code should prefer the class-based helpers because they are easier to organize, discover and extend.

## PHPMaker 2026 built-ins reused

Depending on the module, this package reuses PHPMaker functions such as:

- `ClientUrl()`
- `JsonEncode()`
- `HashPassword()`
- `VerifyPassword()`
- `Encrypt()` / `Decrypt()`
- `UrlBase64Encode()` / `UrlBase64Decode()`
- `RemoveXss()`
- `CurrentDate()` / `CurrentDateTime()`
- `DateDiff()`
- `Conn()`
- `ExecuteScalar()`
- `ExecuteRow()`
- `ExecuteRows()`
- `ExecuteStatement()`
- `ExecuteJson()`
- `ContentType()`
- `DataFromBase64Url()`
- `ServerMapPath()`
- `Session()`
- `VaultReveal()`

This keeps PHPMaker's framework behavior in PHPMaker instead of maintaining copied framework code.

## Project-specific functions

Functions tied to a specific schema or external service should stay in the application or a dedicated service package. Examples include wallet billing, `serviceusage`, CashSMS routing, OCR training tables and project-specific API route tables.
