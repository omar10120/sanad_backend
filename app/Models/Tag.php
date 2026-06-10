<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Tag extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'order',
        'subject_id',
        'is_exam',
    ];

    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'tag_has_question', 'tag_id', 'question_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
//
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
    }

    /**
     * Update order of tags within a subject
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
