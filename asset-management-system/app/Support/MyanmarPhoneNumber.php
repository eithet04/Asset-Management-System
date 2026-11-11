<?php

namespace App\Support;

final class MyanmarPhoneNumber
{
    public static function normalize(string $phone): ?string
    {
        $number = preg_replace('/[\s().-]/', '', trim($phone));
        if (! is_string($number)) {
            return null;
        }

        if (preg_match('/^09([2-9]\d{6,8})$/', $number, $matches)
            || preg_match('/^\+?959([2-9]\d{6,8})$/', $number, $matches)) {
            return '+959'.$matches[1];
        }

        return null;
    }
}
