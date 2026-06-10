<?php

namespace App\Traits;

use Exception;

trait HasDeletionPrevention
{
    /**
     * Check if the model can be deleted
     */
    abstract public function canBeDeleted(): bool;

    /**
     * Boot method to add model events for deletion prevention
     */
    protected static function bootHasDeletionPrevention()
    {
        static::deleting(function ($model) {
            if (!$model->canBeDeleted()) {
                $modelName = class_basename($model);
                $translationKey = $modelName . '_has_related_data';
                throw new Exception(trans('main_trans.' . $translationKey));
            }
        });
    }

    /**
     * Get a formatted message about why the model cannot be deleted
     */
    public function getDeletionPreventionMessage(): string
    {
        $modelName = class_basename($this);
        $translationKey = $modelName . '_has_related_data';
        return trans('main_trans.' . $translationKey);
    }

    /**
     * Get a formatted message about why the model can be deleted
     */
    public function getDeletionAllowedMessage(): string
    {
        $modelName = class_basename($this);
        $translationKey = $modelName . '_can_be_deleted';
        return trans('main_trans.' . $translationKey);
    }
} 