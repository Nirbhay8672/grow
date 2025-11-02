<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BuilderUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('role.update') ?? false;
    }

    public function rules(): array
    {
        $builderId = $this->route('builder')->id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('builders', 'name')->ignore($builderId)
            ],
            'is_active' => ['boolean'],
        ];
    }
}

