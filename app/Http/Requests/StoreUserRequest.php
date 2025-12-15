<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|string|email|max:255|unique:users, email',
            'password' => 'required|string|min:5|max:255',
        ];
    }

    public function messages():array
    {
        return [
            'name.min' => 'O nome deve ter ao menos 3 caracteres.',
            'email.unique' => 'Este e-mail já está sendo usado.',
            'password.min' => 'A senha deve ter ao menos 5 caracteres.'
        ];
    }
}
