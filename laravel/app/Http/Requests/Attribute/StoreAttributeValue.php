<?php

namespace App\Http\Requests\Attribute;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttributeValue extends FormRequest
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
            'value' => 'required|unique:attribute_values,value',
            'slug' => 'nullable|unique:attribute_values,slug',
            'description' => 'nullable|max:50',
            'attribute_id' => 'required|exists:attributes,id',
        ];
    }

    public function messages()
    {
        return __('request.messages');
    }

    public function attributes()
    {
        return [
            'name' => 'Tên thuộc tính',
            'slug' => 'Slug',
            'description' => 'Mô tả',
        ];
    }
}
