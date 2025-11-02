<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BuilderStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('role.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:builders,name'],
            'is_active' => ['boolean'],
        ];
    }
}

