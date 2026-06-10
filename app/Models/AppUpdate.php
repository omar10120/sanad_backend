<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppUpdate extends Model
{
    protected $fillable = [
        'version',
        'platform',
        'changelog',
        'is_force_update',
        'update_url',
    ];
}
