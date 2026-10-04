<?php

declare(strict_types=1);

function ReleaseSession(): void
{
    $session = _CommonFunctionPhpMakerCall("Session");
    if ($session && $session->isStarted()) {
        $session->save();
    }
}
