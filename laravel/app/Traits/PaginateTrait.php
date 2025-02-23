<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait PaginateTrait
{
    /**
     * Apply filters, sorting, and pagination to the query.
     *
     * @param Builder $query
     * @param array $select Columns to select.
     * @param array $relationships Relationships to load.
     * @param array $conditions Conditions for search, filter, and sort.
     * @param array $where Additional where conditions.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function filterAndPaginate(
        Builder $query,
        array $select = ['*'],
        array $conditions = [],
        array $relationships = [],
        array $where = [],
    ) {
        // logInfo($conditions);
        // Select columns
        $query->select($select);

        // Load relationships
        if (!empty($relationships)) {
            $query->with($relationships);
        }

        // Apply where conditions
        if (!empty($where)) {
            $query->where($where);
        }

        // Apply search condition
        if (!empty($conditions['searchText']) && !empty($conditions['searchField'])) {
            $query->where($conditions['searchField'], 'like', '%' . $conditions['searchText'] . '%');
        }

        // Apply filter condition
        if (isset($conditions['filterCustom'])) {
            $query->where('published', $conditions['filterCustom']);
        }

        // Apply sorting
        $query->orderBy($conditions['sortField'], $conditions['sortOrder']);

        // if ($conditions['deleted_at']) {
        //     $query->onlyTrashed();
        // }

        logInfo($query->toSql());

        // Paginate results
        $pageSize = request('pageSize', 10);
        return $query->paginate($pageSize);
    }
}
