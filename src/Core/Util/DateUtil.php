<?php

namespace Core\Util;

use DateTime;

class DateUtil
{
    public static function timestampToDate(int $timestamp): DateTime {
        $date = new DateTime();
        $date->setTimestamp($timestamp);
        return $date;
    }
}