<?php

namespace App\Http\Requests;

use App\Models\Cartao;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreCartaoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Cartao::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $cartao = $this->route('cartao');
        $idCartao = $cartao instanceof Cartao? $cartao->id:$cartao;
        return [
            'number' => ['required','digits_between:16,19', Rule::unique('cartaos','number')->ignore($idCartao)],
            'data_validade' => 'required|date_format:m/y',
            'cvv' => 'required|size:3',
            'saldo' => 'required|numeric|min:0',
            'status' => 'required|string|in:ATIVO,BLOQUEADO,CANCELADO',
            'user_id' => ['required', 'int', 'exists:users,id'],
        ];
    }

    public function messages():array
    {
        return [
            'number.digits_between' => 'O número do cartão deve ter entre 16 dígitos e 19.',
            'cvv.size' => 'O CVV deve ter 3 dígitos.',
            'saldo.min' => 'O saldo do cartão não deve ser menor que 0.',
            'data_validade.date_format' => 'A data de validade deve ser no formato mm/yy.'
        ];
    }

    public function returnDados(): array
    {
        return $this->validated();
    }
}
