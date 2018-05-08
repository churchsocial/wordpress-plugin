<?php

namespace ChurchSocial;

class Util
{
    public static function getTimezone()
    {
        if (get_option('timezone_string')) {
            return timezone_open(get_option('timezone_string'));
        }

        $offset = get_option('gmt_offset');
        $minutes = ($offset - floor($offset)) * 60;
        $offset = sprintf('%+03d:%02d', $offset, $minutes);

        return timezone_open($offset);
    }

    public static function date($date, $format = null)
    {
        if ($format) {
            return date_create($date, static::getTimezone())->format($format);
        } else {
            return date_create($date, static::getTimezone());
        }
    }
}
