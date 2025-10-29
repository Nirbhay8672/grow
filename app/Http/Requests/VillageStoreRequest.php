<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VillageStoreRequest extends FormRequest
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
                Rule::unique('villages')->where(function ($query) {
                    return $query->where('district_id', $this->district_id)
                        ->where('taluka_id', $this->taluka_id);
                }),
            ],
            'district_id' => ['required', 'exists:districts,id'],
            'taluka_id' => [
                'required',
                'exists:talukas,id',
                Rule::exists('talukas', 'id')->where(function ($query) {
                    return $query->where('district_id', $this->district_id);
                }),
            ],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'A village with this name already exists in the selected district and taluka.',
            'district_id.required' => 'Please select a district.',
            'taluka_id.required' => 'Please select a taluka.',
            'taluka_id.exists' => 'The selected taluka does not belong to the selected district.',
        ];
    }
}

