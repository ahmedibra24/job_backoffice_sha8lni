<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class jobCategoryRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:job_categories,name,'.$this->input('id'),
            //? original way without input id
            // 'name' => 'required|string|max:255|unique:job_categories,name,'.$this->route('category'),
        ];
    }
    public function messages(): array
    {
        return [
            'name.required' => 'Category name is required.',
            'name.string' => 'Category name must be a string.',
            'name.max' => 'Category name must be less than 255 characters.',
            'name.unique' => 'Category name must be unique.',
        ];
    }
}
