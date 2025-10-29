<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('user.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'state_id' => ['nullable', 'exists:states,id'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'is_active' => ['boolean'],
            'password' => ['required', 'string', 'min:8'],
        ];
    }
}