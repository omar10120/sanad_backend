<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static int toSeconds(string $time)
 * @method static string fromSeconds(int $seconds)
 * @method static string add(string $baseTime, string $addTime)
 * @method static string sub(string $baseTime, string $subTime)
 * @method static int parse(string $time)
 */
class Time extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'time.helper';
    }
}