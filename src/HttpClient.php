<?php

declare(strict_types=1);

namespace Bishu\CommonFunction;

final class HttpClient
{
    public static function request(string $url, string|array $data = '', string $method = 'GET', array $options = []): mixed
    {
        return PhpMaker::call('ClientUrl', $url, $data, strtoupper($method), $options);
    }

    public static function get(string $url, array $query = [], array $options = []): mixed
    {
        return self::request($url, $query, 'GET', $options);
    }

    public static function post(string $url, string|array $data = '', array $options = []): mixed
    {
        return self::request($url, $data, 'POST', $options);
    }

    public static function json(string $url, array|string $data = [], string $method = 'POST', array $options = []): mixed
    {
        $method = strtoupper($method);
        if ($method !== 'GET' && is_array($data)) {
            $data = JsonHelper::encode($data);
        }

        $options['headers'] = array_merge(
            ['Accept' => 'application/json'],
            $method === 'GET' ? [] : ['Content-Type' => 'application/json'],
            $options['headers'] ?? []
        );

        return self::request($url, $data, $method, $options);
    }

    public static function jsonDecode(string $url, array|string $data = [], string $method = 'GET', array $options = []): mixed
    {
        $response = self::json($url, $data, $method, $options);
        return is_string($response) ? JsonHelper::tryDecode($response) : $response;
    }

    public static function portStatus(string $host, int $port = 80, int $timeout = 1): bool
    {
        $socket = @fsockopen($host, $port, $errno, $errstr, $timeout);
        if ($socket === false) {
            return false;
        }
        fclose($socket);
        return true;
    }
}
