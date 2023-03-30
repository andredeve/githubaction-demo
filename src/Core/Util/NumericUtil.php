<?php

namespace Core\Util;

class NumericUtil
{
    public static function parseFloat($value) {
        $LocaleInfo = localeconv();
        $value = str_replace($LocaleInfo["mon_thousands_sep"] , "", $value);
        $value = str_replace($LocaleInfo["mon_decimal_point"] , ".", $value);
        return floatval($value);
    }
}