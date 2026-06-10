<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class CodePackage extends Model
{
    protected $fillable = ['name', 'expires_at'];

    public function codes(): HasMany
    {
        return $this->hasMany(Code::class, 'package_id');
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'code_package_subject', 'code_package_id', 'subject_id');
    }
}
