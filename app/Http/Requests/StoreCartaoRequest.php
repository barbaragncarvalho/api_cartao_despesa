<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCartaoRequest extends FormRequest
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
            'number' => 'required','digits_between: 16,19',
            'data_validade' => 'required|date_format:d/m/Y',
            'cvv' => 'required|size:3',
            'saldo' => 'required|numeric|min:0',
            'status' => 'required|string|in:ATIVO,BLOQUEADO,CANCELADO',
            'user_id' => 'required|int|user.exists:users,id',
        ];
    }

    public function messages():array
    {
        return [
            'number.min' => 'O número do cartão deve ter ao menos 16 dígitos.',
            'cvv.size' => 'O CVV deve ter 3 dígitos.',
            'saldo.min' => 'O saldo do cartão não deve ser menor que 0.'
        ];
    }
}
