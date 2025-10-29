<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TalukaUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('role.update') ?? false;
    }

    public function rules(): array
    {
        $taluka = $this->route('taluka');
        
        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('talukas')->where(function ($query) use ($taluka) {
                    return $query->where('district_id', $this->district_id ?? $taluka->district_id);
                })->ignore($taluka->id),
            ],
            'district_id' => ['sometimes', 'required', 'exists:districts,id'],
            'is_active' => ['sometimes', 'boolean'],
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

