<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;

class Lesson extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'order',
        'subject_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];


    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function questionGroups(): HasMany
    {
        return $this->hasMany(QuestionGroup::class)->orderBy('order');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // علاقة عبر مجموعات الأسئلة للحصول على جميع أسئلة الدرس
    public function questions(): HasManyThrough
    {
        return $this->hasManyThrough(
            Question::class,
            QuestionGroup::class,
            'lesson_id', // مفتاح خارجي في جدول مجموعات الأسئلة
            'question_group_id', // مفتاح خارجي في جدول الأسئلة
            'id', // مفتاح محلي في جدول الدروس
            'id' // مفتاح محلي في جدول مجموعات الأسئلة
        );
    }

    /**
     * Check if the lesson can be deleted
     */
    public function canBeDeleted(): bool
    {
        return $this->questionGroups()->count() === 0;
    }

    /**
     * تحديث ترتيب المجموعات
     */
    public function updateGroupsOrder(array $orderedGroupIds): void
    {
        DB::transaction(function () use ($orderedGroupIds) {
            foreach ($orderedGroupIds as $order => $groupId) {
                QuestionGroup::where('id', $groupId)
                    ->where('lesson_id', $this->id)
                    ->update(['order' => $order + 1]);
            }
        });
    }

    public function getAllQuestions()
    {
        return Question::whereHas('questionGroup', function($query) {
            $query->where('lesson_id', $this->id);
        })->get();
    }

    /**
     * Boot method to add model events
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->order)) {
                $lastOrder = self::where('subject_id', $model->subject_id)->max('order') ?? 0;
                $model->order = $lastOrder + 1;
            }
        });

        static::deleting(function ($lesson) {
            if (!$lesson->canBeDeleted()) {
                throw new Exception(trans('main_trans.Lesson_has_related_question_groups'));
            }
        });
    }

    /**
     * Update order of lessons within a subject
     */
    public function updateOrder(array $orderedIds): void
    {
        DB::transaction(function () use ($orderedIds) {
            foreach ($orderedIds as $order => $id) {
                self::where('id', $id)->where('subject_id', $this->subject_id)
                    ->update(['order' => $order + 1]);
            }
        });
    }
}
