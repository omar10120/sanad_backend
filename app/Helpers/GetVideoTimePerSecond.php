<?php 

namespace App\Helpers;

class GetVideoTimePerSecond
{
    public static function getVideoTimePerSecond($videoTime)
    {
        if (empty($videoTime)) {
            return 0;
        }
        $parts = explode(':', $videoTime);
        $seconds = 0;
        $count = count($parts);
        if ($count == 3) {
            $seconds = intval($parts[0]) * 3600 + intval($parts[1]) * 60 + intval($parts[2]);
        } elseif ($count == 2) {
            $seconds = intval($parts[0]) * 60 + intval($parts[1]);
        } elseif ($count == 1) {
            $seconds = intval($parts[0]); 
        } else {
            $seconds = 0;
        }
        return $seconds;
    }
}