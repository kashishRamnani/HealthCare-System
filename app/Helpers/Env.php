<?php

namespace App\Helpers;


class Env
{
    private static array $env = [];

    public static function load(string $path): void
    {
        if (!file_exists($path)) {
            throw new \Exception('.env file not found');
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            if (str_starts_with(trim($line), '#')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            self::$env[trim($key)] = trim($value, "\"");
        }
    }

    public static function get(string $key, $default = null)
    {
        return self::$env[$key] ?? $default;
    }
}
