<?php

declare(strict_types=1);

function HtmlExtractInputValue(string $html, string $field): ?string
{
    if (!preg_match_all('~<input\b[^>]*>~is', $html, $matches)) return null;
    foreach ($matches[0] as $tag) {
        $id = HtmlExtractAttribute($tag, "id");
        $name = HtmlExtractAttribute($tag, "name");
        if (($id !== null && strcasecmp($id, $field) === 0) || ($name !== null && strcasecmp($name, $field) === 0)) {
            return HtmlExtractAttribute($tag, "value");
        }
    }
    return null;
}

function HtmlExtractAttribute(string $tag, string $attribute): ?string
{
    $attribute = preg_quote($attribute, "~");
    $pattern = '~\b' . $attribute . '\s*=\s*' . '(?:"([^"]*)"|\'([^\']*)\'|([^\s>]+))~is';
    if (!preg_match($pattern, $tag, $match)) return null;
    foreach ([1, 2, 3] as $index) {
        if (array_key_exists($index, $match)) return CleanHtmlText($match[$index]);
    }
    return null;
}

function HtmlExtractStyledTagText(string $html, string $tagName, string $styleNeedle): ?string
{
    $tagName = preg_quote($tagName, "~");
    $pattern = '~<' . $tagName . '\b([^>]*)>(.*?)</' . $tagName . '>~is';
    if (!preg_match_all($pattern, $html, $matches, PREG_SET_ORDER)) return null;
    $needle = NormalizeHtmlStyle($styleNeedle);
    foreach ($matches as $match) {
        $style = HtmlExtractAttribute('<x ' . $match[1] . '>', "style");
        if ($style !== null && str_contains(NormalizeHtmlStyle($style), $needle)) return CleanHtmlText($match[2]);
    }
    return null;
}

function HtmlExtractStyledTextAnyTag(string $html, string $styleNeedle): ?string
{
    $pattern = '~<([a-z][a-z0-9]*)\b([^>]*\bstyle\s*=\s*(?:"[^"]*"|\'[^\']*\')[^>]*)>(.*?)</\1>~is';
    if (!preg_match_all($pattern, $html, $matches, PREG_SET_ORDER)) return null;
    $needle = NormalizeHtmlStyle($styleNeedle);
    foreach ($matches as $match) {
        $style = HtmlExtractAttribute('<x ' . $match[2] . '>', "style");
        if ($style !== null && str_contains(NormalizeHtmlStyle($style), $needle)) return CleanHtmlText($match[3]);
    }
    return null;
}

function HtmlExtractTagText(string $html, string $tagName): ?string
{
    $tagName = preg_quote($tagName, "~");
    return preg_match('~<' . $tagName . '\b[^>]*>(.*?)</' . $tagName . '>~is', $html, $match)
        ? CleanHtmlText($match[1])
        : null;
}

function NormalizeHtmlStyle(string $value): string
{
    return strtolower(preg_replace('/\s+/', '', $value) ?? $value);
}

function CleanHtmlText(?string $value): ?string
{
    if ($value === null) return null;
    $value = strip_tags($value);
    $value = _CommonFunctionPhpMakerCall("ConvertToString", _CommonFunctionPhpMakerCall("HtmlDecode", $value));
    $cleaned = preg_replace('/\s+/u', ' ', $value);
    if ($cleaned !== null) $value = $cleaned;
    $value = trim($value);
    return _CommonFunctionPhpMakerCall("IsEmpty", $value) ? null : $value;
}
