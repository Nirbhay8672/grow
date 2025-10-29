<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CityUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('role.update') ?? false;
    }

    public function rules(): array
    {
        $city = $this->route('city');
        
        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('cities')->where(function ($query) {
                    return $query->where('state_id', $this->state_id ?? $city->state_id);
                })->ignore($city->id),
            ],
            'state_id' => ['sometimes', 'required', 'exists:states,id'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'A city with this name already exists in the selected state.',
            'state_id.required' => 'Please select a state.',
            'state_id.exists' => 'The selected state is invalid.',
        ];
    }
}