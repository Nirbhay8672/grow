<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ConstructionTypeUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('role.update') ?? false;
    }

    public function rules(): array
    {
        $constructionTypeId = $this->route('constructionType')->id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('construction_types', 'name')->ignore($constructionTypeId)
            ],
            'is_active' => ['boolean'],
        ];
    }
}

