<?php

namespace App\Http\Requests\Catalogue;

use Illuminate\Foundation\Http\FormRequest;

class StoreCatalogue extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|unique:catalogues,name',
            'slug' => 'nullable|unique:catalogues,slug',
            'description' => 'nullable|string|max:100',
            'parent_id' => 'nullable|exists:catalogues,id',
            'image' => 'nullable|string',
            'seo_title' => 'nullable|string',
            'seo_description' => 'nullable|string',
            'seo_keywords' => 'nullable|array',
        ];
    }

    public function messages()
    {
        return __('request.messages');
    }

    public function attributes()
    {
        return [
            'name' => 'Tên danh mục',
            'slug' => 'Slug',
            'description' => 'Mô tả',
            'parent_id' => 'Danh mục cha'
        ];
    }
}
