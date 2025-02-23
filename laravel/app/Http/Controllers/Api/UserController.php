<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\UserCollection;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Services\QueryBuilder;

class UserController extends Controller
{

    protected $queryBuilder;

    public function __construct()
    {
        $this->queryBuilder = new QueryBuilder(User::class);
    }

    public function index(Request $request)
    {
        $columns = ['id', 'name', 'email', 'role_id', 'published', 'created_at'];

        $query = $this->queryBuilder->buildQuery(
            $columns,
            ['role:id,name'],
            'role_id',
            $request,
        );

        return $this->queryBuilder->processDataTable($query, function ($dataTable) {
            return $dataTable
                ->addColumn('role_id', fn($row) => $row->role->name)
                ->filterColumn('role_id', function ($query, $keyword) {
                    $query->whereHas('role', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
                });
        }, 'published');
    }
}
