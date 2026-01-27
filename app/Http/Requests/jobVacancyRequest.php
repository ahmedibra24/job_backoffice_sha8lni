<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class jobVacancyRequest extends FormRequest
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
        //? if the user is recruiter, company_id is not required as it will be assigned automatically
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'salary' => 'required|numeric|min:0',
            'type' => 'required|in:Full-time,Contract,Remote,Hybrid',
            'company_id' => Auth::user()->role === 'recruiter' ? 'nullable|exists:companies,id' : 'required|exists:companies,id',
            'category_id' => 'required|exists:job_categories,id',
        ];
    }
    public function messages(): array
    {
        return [
            'title.required' => 'Job title is required.',
            'title.string' => 'Job title must be a string.',
            'title.max' => 'Job title must be less than 255 characters.',
            'description.required' => 'Job description is required.',
            'description.string' => 'Job description must be a string.',
            'location.required' => 'Location is required.',
            'location.string' => 'Location must be a string.',
            'location.max' => 'Location must be less than 255 characters.',
            'salary.required' => 'Salary is required.',
            'salary.numeric' => 'Salary must be a number.',
            'salary.min' => 'Salary must be at least 0.',
            'type.required' => 'Job type is required.',
            'type.in' => 'Job type must be one of the following: Full-time, Contract, Remote, Hybrid.',
            'company_id.required' => 'Company is required.',
            'company_id.exists' => 'Selected company does not exist.',
            'category_id.required' => 'Category is required.',
            'category_id.exists' => 'Selected category does not exist.',
        ];
    }
}
