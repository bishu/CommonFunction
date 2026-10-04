<?php

declare(strict_types=1);

function LogCustomError(string $name, string $message, array $context = []): void
{
    static $loggers = [];
    $name = strtolower(preg_replace('/[^a-zA-Z0-9_-]/', '', trim($name)) ?? "");
    if ($name === "") $name = "custom";

    if (!isset($loggers[$name])) {
        $logger = new \Monolog\Logger($name);
        $handler = new \Monolog\Handler\StreamHandler(
            _CommonFunctionPhpMakerCall("ServerMapPath", "var/log/{$name}.log", true),
            \Monolog\Level::Error
        );
        $formatter = new \Monolog\Formatter\LineFormatter(
            "[%datetime%] %message% %context%\n",
            "Y-m-d H:i:s",
            true,
            true
        );
        $handler->setFormatter($formatter);
        $logger->pushHandler($handler);
        $loggers[$name] = $logger;
    }

    $loggers[$name]->error($message, $context);
}
