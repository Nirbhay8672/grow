<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubCategoryUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('role.update') ?? false;
    }

    public function rules(): array
    {
        $subCategory = $this->route('subCategory');
        
        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('sub_categories')->where(function ($query) {
                    return $query->where('category_id', $this->category_id ?? $subCategory->category_id);
                })->ignore($subCategory->id),
            ],
            'category_id' => ['sometimes', 'required', 'exists:categories,id'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The sub category name is required.',
            'name.unique' => 'This sub category name already exists for this category.',
            'category_id.required' => 'Please select a category.',
            'category_id.exists' => 'The selected category is invalid.',
        ];
    }
}
