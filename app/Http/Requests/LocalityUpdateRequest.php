<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LocalityUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('role.update') ?? false;
    }

    public function rules(): array
    {
        $locality = $this->route('locality');
        
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'state_id' => ['sometimes', 'required', 'exists:states,id'],
            'city_id' => [
                'sometimes',
                'required',
                'exists:cities,id',
                Rule::exists('cities', 'id')->where(function ($query) use ($locality) {
                    return $query->where('state_id', $this->state_id ?? $locality->state_id);
                }),
            ],
            'zip_code' => [
                'sometimes',
                'required',
                'string',
                'max:10',
                Rule::unique('localities', 'zip_code')->ignore($locality->id),
                Rule::unique('localities')->where(function ($query) use ($locality) {
                    return $query->where('state_id', $this->state_id ?? $locality->state_id)
                        ->where('city_id', $this->city_id ?? $locality->city_id)
                        ->where('zip_code', $this->zip_code ?? $locality->zip_code);
                })->ignore($locality->id),
            ],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'zip_code.unique' => 'This zip code already exists. Zip code must be unique.',
            'city_id.exists' => 'The selected city does not belong to the selected state.',
            'state_id.required' => 'Please select a state.',
            'city_id.required' => 'Please select a city.',
        ];
    }
}

