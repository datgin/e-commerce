<?php

namespace App\Http\Requests\Attribute;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAttribute extends FormRequest
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
            'name' => 'required|unique:attributes,name,' . $this->attribute,
            'slug' => 'nullable|unique:attributes,slug,' . $this->attribute,
            'published' => 'required',
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
            'published' => 'Trạng thái',
        ];
    }
}
