<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DistrictStoreRequest extends FormRequest
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
                Rule::unique('districts')->where(function ($query) {
                    return $query->where('state_id', $this->state_id);
                }),
            ],
            'state_id' => ['required', 'exists:states,id'],
            'is_active' => ['boolean'],
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

