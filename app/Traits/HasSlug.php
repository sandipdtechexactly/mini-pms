<?php

namespace App\Traits;

use Illuminate\Support\Str;

/**
 * Trait HasSlug
 * 
 * Provides functionality for generating and handling slugs for models.
 */
trait HasSlug
{
    /**
     * Boot the trait.
     *
     * @return void
     */
    public static function bootHasSlug()
    {
        static::creating(function ($model) {
            $model->generateSlug();
        });

        static::updating(function ($model) {
            $model->generateSlug();
        });
    }

    /**
     * Generate a URL friendly slug from the given string.
     *
     * @param string $value
     * @return string
     */
    public function createSlug(string $value): string
    {
        $slug = Str::slug($value);
        $count = 2;
        $originalSlug = $slug;

        while ($this->slugExists($slug)) {
            $slug = $originalSlug . '-' . $count++;
        }

        return $slug;
    }

    /**
     * Check if a slug already exists in the database.
     *
     * @param string $slug
     * @return bool
     */
    protected function slugExists(string $slug): bool
    {
        $query = static::where($this->getSlugColumnName(), $slug);

        if ($this->exists) {
            $query->where($this->getKeyName(), '!=', $this->getKey());
        }

        if (in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($this))) {
            $query->withTrashed();
        }

        return $query->exists();
    }

    /**
     * Generate a slug for the model.
     *
     * @return void
     */
    public function generateSlug(): void
    {
        $slugColumn = $this->getSlugColumnName();
        $sourceColumn = $this->getSlugSourceColumn();

        if (empty($this->{$slugColumn}) && !empty($this->{$sourceColumn})) {
            $this->{$slugColumn} = $this->createSlug($this->{$sourceColumn});
        }
    }

    /**
     * Get the name of the slug column.
     *
     * @return string
     */
    protected function getSlugColumnName(): string
    {
        return property_exists($this, 'slugColumn') 
            ? $this->slugColumn 
            : 'slug';
    }

    /**
     * Get the name of the source column for generating the slug.
     *
     * @return string
     */
    protected function getSlugSourceColumn(): string
    {
        return property_exists($this, 'slugSource')
            ? $this->slugSource
            : 'name';
    }

    /**
     * Find a model by its slug.
     *
     * @param string $slug
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public static function findBySlug(string $slug)
    {
        return static::where((new static)->getSlugColumnName(), $slug)->first();
    }

    /**
     * Find a model by its slug or fail.
     *
     * @param string $slug
     * @return \Illuminate\Database\Eloquent\Model
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public static function findBySlugOrFail(string $slug)
    {
        return static::where((new static)->getSlugColumnName(), $slug)->firstOrFail();
    }
}
