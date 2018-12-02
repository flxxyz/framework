<?php

namespace Col\Lib;


class Hash
{
    public static function make($value, $cost = 6)
    {
        return password_hash($value, PASSWORD_BCRYPT, [
            'cost' => $cost,
        ]);
    }

    public static function check($value, $hashedValue)
    {
        if (mb_strlen($hashedValue) === 0) {
            return false;
        }

        return password_verify($value, $hashedValue);
    }

    public static function info($hash)
    {
        return password_get_info($hash);
    }
}