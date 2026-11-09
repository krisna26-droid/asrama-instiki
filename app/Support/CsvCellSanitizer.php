<?php

namespace App\Support;

final class CsvCellSanitizer
{
    public static function sanitize(mixed $value): mixed
    {
        if (!is_string($value)) {
            return $value;
        }

        return preg_match('/^[=+\-@\t\r\n]/u', $value) === 1 ? "'" . $value : $value;
    }
}
