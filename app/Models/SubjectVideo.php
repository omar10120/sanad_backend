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
        'link',
        'is_active',
        'description',
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
            ->withPivot('order')
            ->orderByPivot('order');
    }

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
