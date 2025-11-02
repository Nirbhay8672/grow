<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OwnerTypeUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('role.update') ?? false;
    }

    public function rules(): array
    {
        $ownerTypeId = $this->route('ownerType')->id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('owner_types', 'name')->ignore($ownerTypeId)
            ],
            'is_active' => ['boolean'],
        ];
    }
}

