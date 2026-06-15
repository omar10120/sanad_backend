<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CodePackage extends Model
{
    protected $fillable = ['name', 'expires_at'];

    public function codes(): HasMany
    {
        return $this->hasMany(Code::class, 'package_id');
    }

    public function codePackageSubjects(): HasMany
    {
        return $this->hasMany(CodePackageSubject::class, 'code_package_id');
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'code_package_subject', 'code_package_id', 'subject_id')
            ->withPivot('unit_id');
    }
}
