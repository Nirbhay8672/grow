<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LocalityStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('role.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'state_id' => ['required', 'exists:states,id'],
            'city_id' => [
                'required',
                'exists:cities,id',
                Rule::exists('cities', 'id')->where(function ($query) {
                    return $query->where('state_id', $this->state_id);
                }),
            ],
            'zip_code' => [
                'required',
                'string',
                'max:10',
                Rule::unique('localities', 'zip_code'),
                Rule::unique('localities')->where(function ($query) {
                    return $query->where('state_id', $this->state_id)
                        ->where('city_id', $this->city_id)
                        ->where('zip_code', $this->zip_code);
                }),
            ],
            // Ensure combination of all fields is unique as requested
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('localities')->where(function ($query) {
                    return $query->where('state_id', $this->state_id)
                        ->where('city_id', $this->city_id)
                        ->where('zip_code', $this->zip_code)
                        ->where('name', $this->name);
                }),
            ],
            'is_active' => ['boolean'],
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

