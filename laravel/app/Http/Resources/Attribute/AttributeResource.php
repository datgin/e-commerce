<?php

namespace App\Http\Resources\Attribute;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttributeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'key' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'published' => $this->published,
            'items' => implode(', ', $this->attributeValues->pluck('value')->toArray())
        ];
    }
}
