<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;

class QueryBuilder
{
    protected $modelClass;

    public function __construct(string $modelClass)
    {
        $this->modelClass = $modelClass;
    }

    public function buildQuery(array $columns, array $relations = [], string $sortRelationColumn = null, $request = null)
    {
        $query = $this->modelClass::query()->select($columns);

        if (count($relations) > 0) {
            $query->with($relations);
        }

        if ($request) {
            $query->when(empty($request->order), fn($q) => $q->latest('id'))
                ->when(!empty($request->order) && $request->order[0]['name'] == $sortRelationColumn, function ($q) use ($request) {
                    logInfo($request->order[0]['name'] . ' is not ' . $request->order[0]['dir']);
                    return $q->orderBy($request->order[0]['name'], $request->order[0]['dir']);
                });
        }


        return $query;
    }

    public function processDataTable($query, callable $customColumns, string $rawColumn = null)
    {
        $dataTable = DataTables()->eloquent($query);

        // Gọi callback để xử lý phần tùy chỉnh
        $dataTable = $customColumns($dataTable);

        return $dataTable
            ->addColumn('checkbox', fn($row) => "<input type='checkbox' class='row-checkbox form-check-input' value='{$row->id}'>")
            ->editColumn('published', function ($row) {
                return '
                    <div class="text-center">
                        <label class="switch">
                            <input class="toggle-publish" data-id="' . $row->id . '" type="checkbox" ' . ($row->published == 1 ? 'checked' : '') . ' />
                            <span class="slider"></span>
                        </label>
                    </div>
        ';
            })
            ->rawColumns(['checkbox', 'published'])
            ->make(true);
    }
}
