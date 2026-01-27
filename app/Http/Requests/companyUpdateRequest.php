<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class companyUpdateRequest extends FormRequest
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
        //? to ignore unique validation for the current company being updated
        return [
            'name' => 'required|string|max:255|unique:companies,name,'.$this->input('id'),
            'email' => 'required|email|max:255|unique:companies,email,'.$this->input('id') ,
            'address' => 'required|string|max:500',
            'industry' => 'required|string|max:255',
            'website' => 'nullable|url|max:255',
            'owner_name' => 'required|string|max:255',
            'owner_password' => 'nullable|string|min:8|max:255',

        ];
    }
        public function messages(): array
    {
        return [
            'name.required' => 'Category name is required.',
            'name.string' => 'Category name must be a string.',
            'name.max' => 'Category name must be less than 255 characters.',
            'name.unique' => 'Category name must be unique.',
            'address.required' => 'Address is required.',
            'address.string' => 'Address must be a string.',
            'address.max' => 'Address must be less than 500 characters.',
            'industry.required' => 'Industry is required.',
            'industry.string' => 'Industry must be a string.',
            'industry.max' => 'Industry must be less than 255 characters.', 
            'website.url' => 'Website must be a valid URL.',
            'website.max' => 'Website must be less than 255 characters.',
            'email.required' => 'Email is required.',
            'email.email' => 'Email must be a valid email address.',
            'email.max' => 'Email must be less than 255 characters.',
            'email.unique' => 'Email must be unique.',
            'owner_name.required' => 'Owner name is required.',
            'owner_name.string' => 'Owner name must be a string.',
            'owner_name.max' => 'Owner name must be less than 255 characters.',
            'owner_password.string' => 'Owner password must be a string.',
            'owner_password.min' => 'Owner password must be at least 8 characters.',

        ];
    }



}
