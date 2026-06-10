<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Code extends Model
{
    protected $fillable = [
        'code',
        'package_id',
        'student_id',
    ];

    public function package(): BelongsTo
    {
        return $this->belongsTo(CodePackage::class, 'package_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class)->withTrashed();
    }
}
