<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DistrictUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('role.update') ?? false;
    }

    public function rules(): array
    {
        $district = $this->route('district');
        
        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('districts')->where(function ($query) use ($district) {
                    return $query->where('state_id', $this->state_id ?? $district->state_id);
                })->ignore($district->id),
            ],
            'state_id' => ['sometimes', 'required', 'exists:states,id'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'A district with this name already exists in the selected state.',
            'state_id.required' => 'Please select a state.',
            'state_id.exists' => 'The selected state is invalid.',
        ];
    }
}

