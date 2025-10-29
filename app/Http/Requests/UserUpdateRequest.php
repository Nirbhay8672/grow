<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('user.update') ?? false;
    }

    public function rules(): array
    {
        $user = $this->route('user');
        
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'username' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'username')->ignore($user->id),
            ],
            'first_name' => ['sometimes', 'required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['sometimes', 'required', 'string', 'max:255'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'email' => [
                'sometimes',
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'company_name' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'state_id' => ['nullable', 'exists:states,id'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'is_active' => ['sometimes', 'boolean'],
            'password' => ['nullable', 'string', 'min:8'],
        ];
    }
}