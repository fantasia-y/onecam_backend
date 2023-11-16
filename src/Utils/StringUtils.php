<?php

namespace App\Utils;

class StringUtils
{
    /**
     * @throws \Exception
     */
    public static function secureRandomString(int $length): string
    {
        $random_string = '';
        for($i = 0; $i < $length; $i++) {
            $number = random_int(0, 36);
            $character = base_convert($number, 10, 36);
            $random_string .= $character;
        }
        return $random_string;
    }
}