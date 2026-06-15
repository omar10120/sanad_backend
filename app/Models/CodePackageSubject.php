<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CodePackageSubject extends Model
{
    protected $table = 'code_package_subject';

    protected $fillable = [
        'code_package_id',
        'subject_id',
        'unit_id',
    ];

    public function codePackage(): BelongsTo
    {
        return $this->belongsTo(CodePackage::class, 'code_package_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}
