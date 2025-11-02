<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConstructionTypeStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('role.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:construction_types,name'],
            'is_active' => ['boolean'],
        ];
    }
}

