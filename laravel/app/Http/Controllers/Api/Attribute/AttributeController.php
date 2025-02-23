<?php

namespace App\Http\Controllers\Api\Attribute;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attribute\{
    StoreAttribute,
    UpdateAttribute
};
use App\Http\Resources\Attribute\AttributeCollection;
use App\Http\Resources\Attribute\AttributeResource;
use App\Models\Attribute;
use App\Traits\PaginateTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttributeController extends Controller
{
    use PaginateTrait;
    public $attribute;
    public function __construct(Attribute $attribute)
    {
        $this->attribute = $attribute;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $conditions = $this->payload();

        $query = $this->attribute->newQuery();

        $response = new AttributeCollection(
            resource: $this->filterAndPaginate(
                $query,
                ['id', 'name', 'slug', 'published'],
                $conditions,
                ['attributeValues:value,attribute_id']
            )
        );

        return successResponse('Fetching data successfully', $response);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAttribute $request)
    {
        return transaction(function () use ($request) {

            $credentials = $request->validated();

            if (!isset($credentials['slug'])) {
                $credentials['slug'] = generateSlug($credentials['name']);
            }

            $credentials['published'] = $credentials['published'] == 'true' ? true : false;

            $this->attribute->create($credentials);

            return handleResponse('Created successfully', 201);
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $attributeValue = $this->attribute->where('slug', $id)->firstOrFail();

        $response = new AttributeResource($attributeValue);

        return successResponse('Fetched data successfully', $response);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAttribute $request, string $id)
    {
        return transaction(function () use ($request, $id) {

            $credentials = $request->validated();

            if (!isset($credentials['slug'])) {
                $credentials['slug'] = generateSlug($credentials['name']);
            }

            $credentials['published'] = $credentials['published'] == 'true' ? true : false;

            logInfo($credentials);


            $this->attribute->findOrFail($id)->update($credentials);

            return handleResponse('Updated successfully');
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
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
}
