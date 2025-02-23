<?php

namespace App\Http\Controllers\Api\Catalogue;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalogue\{
    StoreCatalogue,
    UpdateCatalogue
};
use App\Http\Resources\Catalogue\CatalogResource;
use App\Http\Resources\Catalogue\CatalogueCollection;
use App\Models\Catalogue;
use App\Traits\PaginateTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;


class CatalogueController extends Controller
{
    use PaginateTrait;
    /**
     * Display a listing of the resource.
     */
    public $catalogue;

    public function __construct(Catalogue $catalogue)
    {
        $this->catalogue = $catalogue;
    }


    public function index(Request $request): JsonResponse
    {
        $conditions = $this->payload();

        if ($request->has('catalogues') && $request->catalogues == true) return $this->getCatalogueIsParent();

        $query = $this->catalogue->newQuery();

        $select = ['id', 'name', 'slug', 'parent_id', 'published'];

        $response = new CatalogueCollection(
            $this->filterAndPaginate(
                $query,
                $select,
                $conditions
            )
        );

        return successResponse('Fetching data successfully', $response);
    }

    public function payload()
    {
        return  [
            'searchField' => request('searchField', 'name'),
            'sortField' => request('sortField', 'id'),
            'sortOrder' => request('sortOrder', 'desc'),
            'searchText' => request()->searchText,
            'filterCustom' => request('filterCustom'),
            'deleted_at' => request('deleted_at'),
        ];
    }


    private function getCatalogueIsParent()
    {
        $catalogues = $this->catalogue->select('name', 'id')->get()->map(function ($catalogue) {
            return [
                'label' => $catalogue->name,
                'value' => $catalogue->id
            ];
        });

        return response()->json(['success' => true, 'message' => 'Data fetched successfully', 'data' => $catalogues], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCatalogue $request)
    {
        return transaction(function () use ($request) {
            $credentials = $request->validated();

            if (empty($credentials['slug'])) {
                $credentials['slug'] = Str::slug($credentials['name']);
            }

            $catalogue = $this->catalogue->create($credentials);

            if (isset($credentials['parent_id'])) {
                $parentCategory = $this->catalogue->find($credentials['parent_id']);
                $parentCategory->appendNode($catalogue);
            } else {
                $catalogue->saveAsRoot();
            }

            return response()->json(['success' => true, 'message' => 'Thao tác thành công'], 201);
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $catalogue = $this->catalogue->findOrFail($id);

        $response = new CatalogResource($catalogue);

        return response()->json([
            'success' => true,
            'message' => 'Fetching data successfully',
            'data' => $response
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCatalogue $request, string $id): JsonResponse
    {
        $credentials = $request->validated();

        $catalogue = $this->catalogue->findOrFail($id);

        $catalogue->update($credentials);

        return handleResponse('Updated Successfully', 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
