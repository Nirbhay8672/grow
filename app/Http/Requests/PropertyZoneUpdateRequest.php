<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PropertyZoneUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('role.update') ?? false;
    }

    public function rules(): array
    {
        $propertyZoneId = $this->route('propertyZone')->id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('property_zones', 'name')->ignore($propertyZoneId)
            ],
            'is_active' => ['boolean'],
        ];
    }
}

