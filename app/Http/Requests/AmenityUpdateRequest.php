<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AmenityUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('role.update') ?? false;
    }

    public function rules(): array
    {
        $amenityId = $this->route('amenity')->id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('amenities', 'name')->ignore($amenityId)
            ],
            'is_active' => ['boolean'],
        ];
    }
}

