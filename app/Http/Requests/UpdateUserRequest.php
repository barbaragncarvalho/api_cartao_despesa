<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $userAAtualizar = $this->route('user');
        if(!$userAAtualizar instanceof User){
            $userAAtualizar = User::findOrFail($userAAtualizar);
        }
        return $this->user()->can('update', $userAAtualizar);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->route('user');
        $userId = $user instanceof User ? $user->id : (int)$user;
        return [
            'name' => 'sometimes|string|min:3|max:255',
            'email' => ['sometimes','string','email','max:255',Rule::unique('users', 'email')->ignore($userId)],
            'password' => 'sometimes|string|min:5|max:255',
            'is_admin' => 'sometimes|boolean',
        ];
    }

    public function returnDados(): array
    {
        return $this->validated();
    }
}
