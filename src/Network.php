<?php

declare(strict_types=1);

function PortStatus(string $ip, int $port = 80, int $timeout = 1): bool
{
    $socket = @fsockopen($ip, $port, $errno, $errstr, $timeout);
    if ($socket === false) return false;
    fclose($socket);
    return true;
}
