<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;

class Type extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'type_has_subject', 'type_id', 'subject_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }


    /**
     * Check if the type can be deleted
     */
    public function canBeDeleted(): bool
    {
        return $this->students()->count() === 0 && 
               $this->subjects()->count() === 0;
    }

    /**
     * Boot method to add model events
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->order)) {
                $lastOrder = self::max('order') ?? 0;
                $model->order = $lastOrder + 1;
            }
        });

        static::deleting(function ($type) {
            if (!$type->canBeDeleted()) {
                throw new Exception(trans('main_trans.Type_has_related_data'));
            }
        });
    }

    /**
     * Update order of types
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
