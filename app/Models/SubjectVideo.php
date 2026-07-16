<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class SubjectVideo extends Model
{
    use SoftDeletes;

    protected $table = 'subjects_video';

    protected $fillable = [
        'name',
        'icon',
        'icon_photo',
        'link',
        'is_active',
        'description',
        'light_color_code',
        'dark_color_code',
        'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function types(): BelongsToMany
    {
        return $this->belongsToMany(Type::class, 'type_has_subject_video', 'subject_video_id', 'type_id');
    }

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class, 'teacher_has_subject_video', 'subject_video_id', 'teacher_id')
            ->where('is_active', true)
            ->withPivot('order')
            ->orderByPivot('order');
    }

    public function checkStudentAccess(int $studentId, ?int $unitId = null): bool
    {
        $student = Student::find($studentId);

        if ($student && in_array($student->type_id, [7, 11], true)) {
            return true;
        }
        
        $unitIds = Unit::whereHas('teacher.subjectVideos', function ($query) {
            $query->where('subjects_video.id', $this->id);
        })->where('is_active', true)->when($unitId, fn ($query) => $query->where('id', $unitId))
            ->pluck('id');

        if ($unitIds->isEmpty()) {
            return false;
        }

        return CodePackage::where('expires_at', '>', now())
            ->whereHas('codes', fn ($query) => $query->where('student_id', $studentId))
            ->whereHas('codePackageSubjects', fn ($query) => $query->whereIn('unit_id', $unitIds))
            ->exists();
    }

    // public function checkStudentAccess($studentId, ?int $unitId = null): bool
    // {
    //     $student = Student::find($studentId);

    //     if ($student && in_array($student->type_id, [7, 11], true)) {
    //         return true;
    //     }

    //     $query = $this->codePackages()
    //         ->whereHas('codes', function ($query) use ($studentId) {
    //             $query->where('student_id', $studentId);
    //         })
    //         ->where('expires_at', '>', now());

    //     if ($unitId !== null) {
    //         $query->wherePivot('unit_id', $unitId);
    //     }

    //     return $query->exists();
    // }

    public function canBeDeleted(): bool
    {
        return $this->teachers()->count() === 0;
    }

    public function updateOrder(array $orderedIds): void
    {
        DB::transaction(function () use ($orderedIds) {
            foreach ($orderedIds as $order => $id) {
                self::where('id', $id)->update(['order' => $order + 1]);
            }
        });
    }

    public function updateTeacherOrder(array $orderedIds): void
    {
        DB::transaction(function () use ($orderedIds) {
            foreach ($orderedIds as $order => $teacherId) {
                $this->teachers()->updateExistingPivot($teacherId, ['order' => $order + 1]);
            }
        });
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->order)) {
                $lastOrder = self::max('order') ?? 0;
                $model->order = $lastOrder + 1;
            }
        });

        static::deleting(function (SubjectVideo $subjectVideo) {
            if (!$subjectVideo->canBeDeleted()) {
                throw new Exception(trans('main_trans.Subject_video_has_related_data'));
            }
        });
    }
}
