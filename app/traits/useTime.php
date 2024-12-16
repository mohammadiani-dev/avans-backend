<?php namespace avansdp\traits;
trait useTime
{
    public static function get_next_time(int $time , $datetimeFormat = 'Y-m-d H:i:s')
    {
        $timestamp = time() + $time;
        $date = new \DateTime('now', wp_timezone());
        $date->setTimestamp($timestamp);

        return $date->format($datetimeFormat);
    }

}