<?php

namespace Core;

class Env
{
    public static function load(string $path): void
    {
        if (!is_file($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if ($lines === false) {
            return;
        }

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            [$name, $value] = array_pad(explode('=', $line, 2), 2, '');
            $name = trim($name);
            $value = trim($value);

            if ($name === '') {
                continue;
            }

            $value = self::normalizeValue($value);

            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
            putenv($name . '=' . $value);
        }
    }

    private static function normalizeValue(string $value): string
    {
        $firstCharacter = substr($value, 0, 1);
        $lastCharacter = substr($value, -1);

        if (
            ($firstCharacter === '"' && $lastCharacter === '"')
            || ($firstCharacter === "'" && $lastCharacter === "'")
        ) {
            return substr($value, 1, -1);
        }

        return $value;
    }
}

