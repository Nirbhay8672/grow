<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PropertySourceUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('role.update') ?? false;
    }

    public function rules(): array
    {
        $propertySourceId = $this->route('propertySource')->id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('property_sources', 'name')->ignore($propertySourceId)
            ],
            'is_active' => ['boolean'],
        ];
    }
}

