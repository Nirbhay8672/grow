<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TalukaStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('role.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('talukas')->where(function ($query) {
                    return $query->where('district_id', $this->district_id);
                }),
            ],
            'district_id' => ['required', 'exists:districts,id'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'A taluka with this name already exists in the selected district.',
            'district_id.required' => 'Please select a district.',
            'district_id.exists' => 'The selected district is invalid.',
        ];
    }
}

