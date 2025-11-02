<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MeasurementUnitUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('role.update') ?? false;
    }

    public function rules(): array
    {
        $measurementUnitId = $this->route('measurementUnit')->id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('measurement_units', 'name')->ignore($measurementUnitId)
            ],
            'is_active' => ['boolean'],
        ];
    }
}

