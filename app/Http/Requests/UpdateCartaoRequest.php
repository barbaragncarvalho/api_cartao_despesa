<?php

namespace App\Http\Requests;

use App\Enums\StatusCartao;
use App\Models\Cartao;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCartaoRequest extends FormRequest
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
        $cartaoId = $this->route('cartao');
        return [
            'number' => ['sometimes','digits_between: 16,19',Rule::unique('cartaos','number')->ignore($cartaoId)],
            'data_validade' => 'sometimes|date_format:m/Y',
            'cvv' => 'sometimes|size:3',
            'saldo' => 'sometimes|numeric|min:0',
            'status' => 'sometimes|string|in:ATIVO,BLOQUEADO,CANCELADO',
            'user_id' => 'sometimes|int|exists:users,id',
        ];
    }
}
