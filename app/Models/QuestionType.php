<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class QuestionType extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'name',
		'type',
	];

	protected $casts = [
		'type' => 'string',
	];

	public const TYPE_AUTOMATION = 'Automation';
	public const TYPE_NOT_AUTOMATION = 'NotAutomation';
	public const TYPE_TRUE_OR_FALSE = 'TrueOrFalse';

	public function questions(): HasMany
	{
		return $this->hasMany(Question::class,'type_id');
	}

	/**
	 * Check if the question type can be deleted
	 */
	public function canBeDeleted(): bool
	{
		return $this->questions()->count() === 0;
	}

	public function scopeAutomation($query)
	{
		return $query->where('type', self::TYPE_AUTOMATION);
	}

	public function scopeNotAutomation($query)
	{
		return $query->where('type', self::TYPE_NOT_AUTOMATION);
	}

    public function scopeTrueOrFalse($query)
    {
        return $query->where('type', self::TYPE_TRUE_OR_FALSE);
    }

	public function isAutomation(): bool
	{
		return $this->type === self::TYPE_AUTOMATION;
	}

	public function isNotAutomation(): bool
	{
		return $this->type === self::TYPE_NOT_AUTOMATION;
	}

    public function isTrueOrFalse(): bool
    {
        return $this->type === self::TYPE_TRUE_OR_FALSE;
    }
}
