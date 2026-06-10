<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Exception;

class QuestionGroup extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'lesson_id',
        'name',
        'order'
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }

    /**
     * Check if the question group can be deleted
     */
    public function canBeDeleted(): bool
    {
        return $this->questions()->count() === 0;
    }

    /**
     * Boot method to add model events
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->order)) {
                $lastOrder = self::where('lesson_id', $model->lesson_id)->max('order') ?? 0;
                $model->order = $lastOrder + 1;
            }
        });
    }

    /**
     * Update the order of question groups within a lesson
     */
    public function updateOrder(array $orderedIds): void
    {
        DB::transaction(function () use ($orderedIds) {
            foreach ($orderedIds as $order => $id) {
                self::where('id', $id)->where('lesson_id', $this->lesson_id)
                    ->update(['order' => $order + 1]);
            }
        });
    }
}
