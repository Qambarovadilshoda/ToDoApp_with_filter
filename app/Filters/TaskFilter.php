<?php

namespace App\Filters;

class TaskFilter
{
    /**
     * Create a new class instance.
     */
    public function apply($query, $filters)
    {
        if (isset($filters['title'])) {
            $query->where('title', $filters['title']);
        }

        if (isset($filters['completed'])) {
            $query->where('completed', $filters['completed']);
        }
        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $query->whereBetween('created_at', [$filters['start_date'], $filters['end_date']]);
        }
        return $query;
    }
}
