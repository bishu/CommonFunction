# bishu/common-function

Private Composer package for reusable PHPMaker 2026 common functions.

This package intentionally uses **plain global functions** so existing PHPMaker code stays simple.

No helper classes, no `use` statements and no project namespace setup are required.

## Usage in PHPMaker

After installing the package, call the functions directly:

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

Existing code can continue to use the same function names.

## Install

Add the private repository to the PHPMaker project's `composer.json`:

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

Then run:

```bash
composer require bishu/common-function:dev-main
```

Composer automatically loads `src/CommonFunction.php`.

## Included public functions

- `PortStatus()`
- `ApiError()`
- `ApiSuccess()`
- `ValidateAadhaar()`
- `ValidateName()`
- `ValidateMobile()`
- `ValidatePan()`
- `ValidateEpic()`
- `ValidateVehicleRegistration()`
- `NormalizeAlphaNumeric()`
- `NormalizeImageBase64()`
- `MaskString()`
- `NormalizeNullableString()`
- `JsonClientUrl()`
- `JsonResponseDecode()`
- `LogCustomError()`
- `ReleaseSession()`
- `SolveCaptcha()`
- `GenerateUpiQr()`
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

## Main rule

Keep reusable functions in `src/CommonFunction.php` and call them normally from PHPMaker:

```php
ValidateMobile($input);
ValidatePan($pan);
NormalizeNullableString($value, 100);
JsonResponseDecode($response);
LogCustomError("api", "Request failed", $context);
```
