<?php

declare(strict_types=1);

namespace Bishu\CommonFunction;

use Doctrine\DBAL\Connection;

final class DatabaseHelper
{
    public static function connection(string $dbid = 'DB'): Connection
    {
        return PhpMaker::call('Conn', $dbid);
    }

    public static function scalar(string $sql, Connection|string|null $connection = null): mixed
    {
        return PhpMaker::call('ExecuteScalar', $sql, $connection);
    }

    public static function row(string $sql, Connection|string|null $connection = null): array|false
    {
        return PhpMaker::call('ExecuteRow', $sql, $connection);
    }

    public static function rows(string $sql, Connection|string|null $connection = null): array|false
    {
        return PhpMaker::call('ExecuteRows', $sql, $connection);
    }

    public static function statement(string $sql, Connection|string|null $connection = null): int
    {
        return (int) PhpMaker::call('ExecuteStatement', $sql, $connection);
    }

    public static function json(string $sql, array|bool|null $options = null, Connection|string|null $connection = null): string
    {
        return (string) PhpMaker::call('ExecuteJson', $sql, $options, $connection);
    }

    public static function fetchOne(string $sql, array $params = [], string $dbid = 'DB'): mixed
    {
        return self::connection($dbid)->executeQuery($sql, $params)->fetchOne();
    }

    public static function fetchAssociative(string $sql, array $params = [], string $dbid = 'DB'): array|false
    {
        return self::connection($dbid)->executeQuery($sql, $params)->fetchAssociative();
    }

    public static function fetchAllAssociative(string $sql, array $params = [], string $dbid = 'DB'): array
    {
        return self::connection($dbid)->executeQuery($sql, $params)->fetchAllAssociative();
    }

    public static function execute(string $sql, array $params = [], string $dbid = 'DB'): int
    {
        return self::connection($dbid)->executeStatement($sql, $params);
    }

    public static function transaction(callable $callback, string $dbid = 'DB'): mixed
    {
        return self::connection($dbid)->transactional($callback);
    }
}
