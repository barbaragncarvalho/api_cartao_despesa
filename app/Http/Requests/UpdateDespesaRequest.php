<?php

namespace App\Http\Requests;

use App\Models\Despesa;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDespesaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $despesaAAtualizar = $this->route('despesa');
        if(!$despesaAAtualizar instanceof Despesa){
            $despesaAAtualizar = Despesa::findOrFail($despesaAAtualizar);
        }
        return $this->user()->can('update', $despesaAAtualizar);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'descricao' => 'sometimes|string',
            'valor' => 'sometimes|numeric|min:0',
            'cartao_id' => 'sometimes|int|exists:cartaos,id'
        ];
    }

    public function returnDados(): array
    {
        return $this->validated();
    }
}
