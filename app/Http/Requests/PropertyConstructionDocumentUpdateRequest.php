<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PropertyConstructionDocumentUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('role.update') ?? false;
    }

    public function rules(): array
    {
        $propertyConstructionDocumentId = $this->route('propertyConstructionDocument')->id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('property_construction_documents', 'name')->ignore($propertyConstructionDocumentId)
            ],
            'is_active' => ['boolean'],
        ];
    }
}

