# bishu/common-function

Composer package for reusable PHPMaker 2026 common functions.

The package intentionally uses **plain global functions** so existing PHPMaker code stays simple:

```php
$mobile = ValidateMobile($input);
```

No helper classes, no `use` statements, and no project namespace setup are required.

## Version

Current stable release: **v1.0.0**

**Unreleased (`main`):** `ValidateIfsc()` is implemented on `main` and covered by regression tests, but is **not** included in the `v1.0.0` release. A compatible minor release (proposed `v1.1.0`) must be tested, approved, tagged and published before pinned projects can install it.

For PHPMaker projects, install the fixed release instead of `dev-main`:

```bash
composer require bishu/common-function:1.0.0
```

This prevents future breaking changes on `main` from affecting older projects.

## GitHub repository (public)

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

This GitHub repository is public. Composer normally does not need GitHub credentials to download it. Never store authentication tokens in the PHPMaker project.

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

## Validation regression test

Run the IFSC validation checks from the repository root using PHP 8.2 or newer:

```bash
php tests/ValidationIfscTest.php
```

## Included public functions

### Validation

- `ValidateAadhaar()`
- `ValidateName()`
- `ValidateMobile()`
- `ValidatePan()`
- `ValidateEpic()`
- `ValidateIfsc()` (`main` only until a new tagged release)
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

## Shared development and contribution policy

This shared library is the central place for **general-purpose PHP functions shared across the CashSMS, CRM, and future PHPMaker projects**. Both `bishu/CashSMS-PhpMaker` and `bishu/CRM-PhpMaker` may contribute reusable functions here. It is **not** an Android dependency.

- **Reuse before adding:** inspect PHPMaker built-in functions and this library's existing helpers; avoid duplicate function names or overlapping behavior.
- **Shared code here, project-specific code there:** contribute reusable validation, normalization, API/JSON, utility, or formatting logic under `src/`. Leave application database queries, credentials, business rules, API routes, UI, and project-specific configuration in the corresponding application repository.
- **Maintain public API compatibility:** preserve existing function signatures and behavior where possible; document changes to return values and dependencies. Follow the versioning rules below when choosing the next release.
- **Use Composer's file autoload:** add helpers to an existing appropriate `src/*.php` file when possible. If introducing a new source file, add it to `composer.json` under `autoload.files`. Do not copy generated PHPMaker `phpfn.php` or project files into this repository.
- **Validate and commit independently:** test compatible PHP versions and relevant callers where possible. Commit shared changes to **`bishu/CommonFunction/main`**, and commit any application integration separately to its own repository.
- **Publish deliberately:** a new commit on `main` does **not** change already pinned projects. Create a new approved stable tag/version after validation, then explicitly update and test each consumer's Composer dependency. Do not change historical tags or silently replace pinned versions with `dev-main`.
- **Protect sensitive information:** never commit API secrets, credentials, production customer data, project-specific signing keys, or Git authentication files.

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
