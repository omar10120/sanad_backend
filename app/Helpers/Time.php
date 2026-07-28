<?php

namespace App\Helpers;

class Time
{
   
    public static function toSeconds(string $time): int
    {
        $parts = explode(':', $time);
        $seconds = 0;
        if (count($parts) === 3) {
            $seconds = (int) $parts[0] * 3600 + (int) $parts[1] * 60 + (int) $parts[2];
        } elseif (count($parts) === 2) {
            $seconds = (int) $parts[0] * 60 + (int) $parts[1];
        } else {
            $seconds = (int) $parts[0];
        }
        return $seconds;
    }

    
    public static function fromSeconds(int $seconds): string
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
    }

    /**
     * Add a time duration to a base TIME string.
     */
    public static function add(string $baseTime, string $addTime): string
    {
        $total = self::toSeconds($baseTime) + self::toSeconds($addTime);
        return self::fromSeconds($total);
    }

    /**
     * Subtract a time duration from a base TIME string.
     * Clamps to 0 (negative results become 0) – remove clamp if you need negative times.
     */
    public static function sub(string $baseTime, string $subTime): string
    {
        $total = self::toSeconds($baseTime) - self::toSeconds($subTime);
        if ($total < 0) {
            $total = 0;
        }
        return self::fromSeconds($total);
    }

    /**
     * Parse a time string to seconds (alias for toSeconds).
     */
    public static function parse(string $time): int
    {
        return self::toSeconds($time);
    }
}