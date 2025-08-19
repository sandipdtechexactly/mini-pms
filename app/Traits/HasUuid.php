<?php

namespace App\Traits;

use Illuminate\Support\Str;

/**
 * Trait HasUuid
 * 
 * Provides functionality for models to use UUIDs as primary keys.
 */
trait HasUuid
{
    /**
     * Boot the trait, adding a creating observer.
     *
     * When persisting a new model instance, we generate a UUID.
     *
     * @return void
     */
    public static function bootHasUuid()
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the value indicating whether the IDs are incrementing.
     *
     * @return bool
     */
    public function getIncrementing()
    {
        return false;
    }

    /**
     * Get the auto-incrementing key type.
     *
     * @return string
     */
    public function getKeyType()
    {
        return 'string';
    }
}
