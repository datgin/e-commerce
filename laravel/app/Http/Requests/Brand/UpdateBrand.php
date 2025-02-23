<?php

namespace App\Http\Requests\Brand;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBrand extends FormRequest
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
            'name' => 'required|unique:brands,name,' . $this->brand,
            'slug' => 'nullable|unique:brands,slug,' . $this->brand,
            'description' => 'nullable|string|max:100',
            'logo' => 'nullable|string',
            'website_url' => 'nullable|string'
        ];
    }

    public function messages()
    {
        return __('request.messages');
    }

    public function attributes()
    {
        return [
            'name' => 'Tên thương hiệu',
            'slug' => 'Slug',
            'description' => 'Mô tả',
            'logo' => 'Hình ảnh'
        ];
    }
}
