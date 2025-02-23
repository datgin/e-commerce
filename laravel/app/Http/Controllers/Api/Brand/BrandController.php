<?php

namespace App\Http\Controllers\Api\Brand;

use App\Http\Controllers\Controller;
use App\Http\Requests\Brand\{
    StoreBrand,
    UpdateBrand
};
use App\Http\Resources\Brand\BrandCollection;
use App\Http\Resources\Brand\BrandResource;
use App\Models\Brand;
use App\Traits\PaginateTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    use PaginateTrait;

    public $brand;
    public function __construct(Brand $brand)
    {
        $this->brand = $brand;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $conditions = $this->payload();

        $query = $this->brand->newQuery();

        $response = new BrandCollection(
            resource: $this->filterAndPaginate(
                $query,
                ['id', 'name', 'slug', 'logo', 'website_url', 'published'],
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBrand $request)
    {
        $creadentials = $request->validated();

        if (!isset($creadentials['slug'])) {
            $creadentials['slug'] = generateSlug($creadentials['name']);
        }

        $this->brand->create($creadentials);

        return handleResponse('Created Successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $catalogue = $this->brand->findOrFail($id);

        $response = new BrandResource($catalogue);

        return successResponse('Fetching data successfully', $response);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBrand $request, string $id)
    {
        $credentials = $request->validated();

        $catalogue = $this->brand->findOrFail($id);

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
