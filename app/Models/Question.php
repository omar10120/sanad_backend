<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Question extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'lesson_id',
        'type_id',
        'question_photo',
        'created_by',
        'text_question',
        'choices',
        'right_choice',
        'is_edited',
        'hint',
        'hint_photo',
        'next_question_id',
        'previous_question_id',
        'question_group_id',
        'order',
    ];

    protected $casts = [
        'text_question'=> 'array',
        'choices'=> 'array',
        'hint'=> 'array',
    ];

    public function reports(): HasMany
    {
        return $this->hasMany(QuestionReport::class);
    }

    public function pendingReports(): HasMany
    {
        return $this->hasMany(QuestionReport::class)->where('status', 'pending');
    }

    public function getPendingReportsCountAttribute(): int
    {
        return $this->pendingReports()->count();
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'tag_has_question', 'question_id', 'tag_id')->withTrashed();
    }

    public function typeQuestion(): BelongsTo
    {
        return $this->belongsTo(QuestionType::class,'type_id');
    }


    public function created_by(): BelongsTo
    {
        return $this->belongsTo(User::class,'created_by');
    }

    public function questionGroup(): BelongsTo
    {
        return $this->belongsTo(QuestionGroup::class);
    }

    protected static function booted(): void
    {
        static::creating(function ($question) {
            if (empty($question->order)) {
                $lastOrder = self::where('question_group_id', $question->question_group_id)
                    ->max('order') ?? 0;
                $question->order = $lastOrder + 1;
            }
        });
    }

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            $model->uuid = Str::uuid();
        });
    }

    /**
     * Update the order of questions within a question group
     */
    public function updateOrder(array $orderedIds): void
    {
        DB::transaction(function () use ($orderedIds) {
            foreach ($orderedIds as $order => $id) {
                self::where('id', $id)->where('question_group_id', $this->question_group_id)
                    ->update(['order' => $order + 1]);
            }
        });
    }
}
