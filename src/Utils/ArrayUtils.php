<?php

namespace App\Utils;

class ArrayUtils
{
    public static function last(array $array)
    {
        return $array[array_key_last($array)];
    }
}