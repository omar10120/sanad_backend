<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Unit extends Model
{
    protected $fillable = [
        'teacher_id',
        'name',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function lessonVideos(): HasMany
    {
        return $this->hasMany(LessonVideo::class, 'unit_id');
    }

    public function codePackageSubjects(): HasMany
    {
        return $this->hasMany(CodePackageSubject::class, 'unit_id');
    }

    public function checkStudentAccess(int $studentId): bool
    {
        $student = Student::find($studentId);

        if ($student && in_array($student->type_id, [7, 11], true)) {
            return true;
        }

        return CodePackage::where('expires_at', '>', now())
            ->whereHas('codePackageSubjects', function ($query) {
                $query->where('unit_id', $this->id);
            })
            ->whereHas('codes', function ($query) use ($studentId) {
                $query->where('student_id', $studentId);
            })
            ->exists();
    }

    public function canBeDeleted(): bool
    {
        return $this->lessonVideos()->count() === 0;
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Unit $unit) {
            if (empty($unit->order)) {
                $lastOrder = self::where('teacher_id', $unit->teacher_id)->max('order') ?? 0;
                $unit->order = $lastOrder + 1;
            }
        });

        static::deleting(function (Unit $unit) {
            if (!$unit->canBeDeleted()) {
                throw new Exception(trans('main_trans.Unit_has_related_data'));
            }
        });
    }

    public function updateOrder(array $orderedIds): void
    {
        DB::transaction(function () use ($orderedIds) {
            foreach ($orderedIds as $order => $id) {
                self::where('id', $id)->where('teacher_id', $this->teacher_id)
                    ->update(['order' => $order + 1]);
            }
        });
    }
}
