<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;

class Subject extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'order',
        'icon',
        'link',
        'teacher',
        'description',
        'is_active',
        'light_color_code',
        'dark_color_code',
        'icon_photo',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_has_subject', 'subject_id', 'user_id')
            ->withTimestamps();
    }

    public function tags(): HasMany
    {
        return $this->hasMany(Tag::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class);
    }

    public function types(): BelongsToMany
    {
        return $this->belongsToMany(Type::class, 'type_has_subject', 'subject_id', 'type_id');
    }

    public function codePackages(): BelongsToMany
    {
        return $this->belongsToMany(CodePackage::class, 'code_package_subject', 'subject_id', 'code_package_id');
    }

    /**
     * Check if the subject can be deleted
     */
    public function canBeDeleted(): bool
    {
        return $this->lessons()->count() === 0 &&
               $this->tags()->count() === 0 ;
    }

    // الحصول على عدد الأسئلة عبر الدروس ومجموعات الأسئلة
    public function questionsCount()
    {
        return $this->lessons()->where('is_active',1)->withCount('questions')->get()->sum('questions_count');
    }

    // دالة للتحقق من صلاحية الطالب
    public function checkStudentAccess($studentId): bool
    {
        $student = Student::find($studentId);

        if($student->type_id == 7 || $student->type_id == 11)
            return true;
            
        return $this->codePackages()
            ->whereHas('codes', function($query) use ($studentId) {
                $query->where('student_id', $studentId);
            })
            ->where('expires_at', '>', now())
            ->exists();
    }

    /**
     * Boot method to add model events
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->order)) {
                $lastOrder = self::max('order') ?? 0;
                $model->order = $lastOrder + 1;
            }
        });

        static::deleting(function ($subject) {
            if (!$subject->canBeDeleted()) {
                throw new Exception(trans('main_trans.Subject_has_related_data'));
            }
        });
    }

    /**
     * Update order of subjects
     */
    public function updateOrder(array $orderedIds): void
    {
        DB::transaction(function () use ($orderedIds) {
            foreach ($orderedIds as $order => $id) {
                self::where('id', $id)->update(['order' => $order + 1]);
            }
        });
    }


}
