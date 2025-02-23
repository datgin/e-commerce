<?php

namespace App\Http\Requests\Catalogue;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCatalogue extends FormRequest
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
        $id = $this->catalogue;
        return [
            'name' => 'required|unique:catalogues,name,' . $id,
            'slug' => 'nullable|unique:catalogues,slug,' . $id,
            'description' => 'nullable|string|max:100',
            'image' => 'nullable|string',
            'parent_id' => 'nullable|exists:catalogues,id',
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
