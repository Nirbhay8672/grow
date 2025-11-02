<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FurnitureTypeUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('role.update') ?? false;
    }

    public function rules(): array
    {
        $furnitureTypeId = $this->route('furnitureType')->id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('furniture_types', 'name')->ignore($furnitureTypeId)
            ],
            'is_active' => ['boolean'],
        ];
    }
}

