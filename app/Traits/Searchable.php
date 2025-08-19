<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

/**
 * Trait Searchable
 * 
 * Provides search functionality for Eloquent models.
 */
trait Searchable
{
    /**
     * Scope a query to apply search filters.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch(Builder $query, array $filters = []): Builder
    {
        $searchable = $this->searchableAttributes();
        
        // Apply search query if provided
        if ($searchTerm = Arr::get($filters, 'search')) {
            $query->where(function ($q) use ($searchable, $searchTerm) {
                foreach ($searchable as $attribute) {
                    $q->orWhere($attribute, 'like', "%{$searchTerm}%");
                }
            });
        }

        // Apply exact matches
        $exactMatches = Arr::except($filters, ['search', 'order_by', 'order_direction', 'per_page']);
        
        foreach ($exactMatches as $field => $value) {
            if (!empty($value) && in_array($field, $searchable)) {
                if (is_array($value)) {
                    $query->whereIn($field, $value);
                } else {
                    $query->where($field, $value);
                }
            }
        }

        // Apply sorting
        $orderBy = Arr::get($filters, 'order_by', $this->getKeyName());
        $orderDirection = Arr::get($filters, 'order_direction', 'asc');
        
        if (in_array($orderBy, $searchable)) {
            $query->orderBy($orderBy, $orderDirection);
        }

        return $query;
    }

    /**
     * Get the searchable attributes for the model.
     *
     * @return array
     */
    protected function searchableAttributes(): array
    {
        if (method_exists($this, 'searchable')) {
            return $this->searchable();
        }

        return property_exists($this, 'searchable') ? $this->searchable : [];
    }

    /**
     * Search and paginate results.
     *
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public static function searchAndPaginate(array $filters = [], int $perPage = 15)
    {
        return static::query()
            ->search($filters)
            ->paginate(
                Arr::get($filters, 'per_page', $perPage)
            );
    }

    /**
     * Get a paginated list of resources with search and filtering.
     *
     * @param array $filters
     * @param array $with
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public static function getFilteredList(array $filters = [], array $with = [], int $perPage = 15)
    {
        return static::with($with)
            ->search($filters)
            ->paginate(
                Arr::get($filters, 'per_page', $perPage)
            );
    }
}
