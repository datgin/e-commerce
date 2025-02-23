<?php

namespace App\Http\Controllers\Api\Attribute;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attribute\StoreAttributeValue;
use App\Http\Requests\Attribute\UpdateAttributeValue;
use App\Http\Resources\Attribute\AttributeValueCollection;
use App\Http\Resources\Attribute\AttributeValueResource;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Traits\PaginateTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttributeValueController extends Controller
{
    use PaginateTrait;
    public $attributeValue, $attribute;
    public function __construct(AttributeValue $attributeValue, Attribute $attribute)
    {
        $this->attributeValue = $attributeValue;
        $this->attribute = $attribute;
    }

    public function index(): JsonResponse
    {
        $conditions = $this->payload();

        $attribute = $this->attribute->query()->where('slug', request('slug'))->firstOrFail();

        $query = $this->attributeValue->newQuery();

        $response = new AttributeValueCollection(
            resource: $this->filterAndPaginate(
                $query,
                ['id', 'value', 'slug', 'description'],
                $conditions,
                [],
                ['attribute_id' => $attribute->id]
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
    public function store(StoreAttributeValue $request)
    {
        return transaction(function () use ($request) {

            $credentials = $request->validated();

            if (!isset($credentials['slug'])) {
                $credentials['slug'] = generateSlug($credentials['value']);
            }

            $this->attributeValue->create($credentials);

            return handleResponse('Created successfully', 201);
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {}

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAttributeValue $request, string $id)
    {
        return transaction(function () use ($request, $id) {

            $credentials = $request->validated();

            if (!isset($credentials['slug'])) {
                $credentials['slug'] = generateSlug($credentials['value']);
            }

            $this->attributeValue->findOrFail($id)->update($credentials);

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
}
