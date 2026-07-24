<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Unit extends Model
{
    use SoftDeletes;

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

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Unit $unit) {
            if (empty($unit->order)) {
                $lastOrder = self::where('teacher_id', $unit->teacher_id)->max('order') ?? 0;
                $unit->order = $lastOrder + 1;
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
