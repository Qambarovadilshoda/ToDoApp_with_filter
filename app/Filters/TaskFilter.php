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
        if (isset($filters['created_at'])) {
            $query->where('created_at', $filters['created_at']);
        }

        return $query;
    }
}
