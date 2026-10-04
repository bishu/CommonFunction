# bishu/common-function

Private Composer package for reusable PHPMaker 2026 common functions.

The package intentionally uses **plain global functions** so existing PHPMaker code stays simple:

```php
$mobile = ValidateMobile($input);
```

No helper classes, no `use` statements, and no project namespace setup are required.

## Version

Current stable release: **v1.0.0**

For PHPMaker projects, install the fixed release instead of `dev-main`:

```bash
composer require bishu/common-function:1.0.0
```

This prevents future breaking changes on `main` from affecting older projects.

## Private GitHub repository

Use the private repository as a Composer VCS repository:

```json
{
    "repositories": {
        "bishu-common-function": {
            "type": "vcs",
            "url": "https://github.com/bishu/CommonFunction.git"
        }
    }
}
```

GitHub authentication should be configured globally in Composer and should not be stored in the PHPMaker project.

## Source structure

```text
src/
├── Internal.php
├── Validation.php
├── String.php
├── Api.php
├── Image.php
├── Network.php
├── Logging.php
├── Session.php
├── Captcha.php
├── Payment.php
└── Html.php
```

Composer loads these files automatically.

## Included public functions

### Validation

- `ValidateAadhaar()`
- `ValidateName()`
- `ValidateMobile()`
- `ValidatePan()`
- `ValidateEpic()`
- `ValidateVehicleRegistration()`

### String / normalization

- `NormalizeAlphaNumeric()`
- `NormalizeNullableString()`
- `MaskString()`

### API / JSON

- `ApiError()`
- `ApiSuccess()`
- `JsonClientUrl()`
- `JsonResponseDecode()`

### Image / network / utilities

- `NormalizeImageBase64()`
- `PortStatus()`
- `LogCustomError()`
- `ReleaseSession()`
- `SolveCaptcha()`
- `GenerateUpiQr()`

### HTML

- `HtmlExtractInputValue()`
- `HtmlExtractAttribute()`
- `HtmlExtractStyledTagText()`
- `HtmlExtractStyledTextAnyTag()`
- `HtmlExtractTagText()`
- `NormalizeHtmlStyle()`
- `CleanHtmlText()`

## PHPMaker 2026 integration

Functions that need PHPMaker features internally resolve and reuse PHPMaker 2026 built-ins such as:

- `IsEmpty()`
- `StartsString()`
- `DataFromBase64Url()`
- `ContentType()`
- `JsonEncode()`
- `ClientUrl()`
- `ServerMapPath()`
- `Session()`
- `ConvertToString()`
- `HtmlDecode()`

PHPMaker's generated `phpfn.php` is **not copied** into this repository.

## Usage in PHPMaker

```php
$mobile = ValidateMobile($input);

if ($mobile === false) {
    return ApiError("Invalid Mobile", "Enter a valid mobile number.");
}

return ApiSuccess("Valid mobile", [
    "mobile" => $mobile,
    "masked" => MaskString($mobile),
]);
```

## Versioning rule

- `1.0.x` — bug fixes without breaking existing calls
- `1.x.0` — new backwards-compatible functions/features
- `2.0.0` — breaking changes such as changing return values, parameters, or removing functions

Do not use `dev-main` in existing production PHPMaker projects when a stable tagged release is available.
