<?php

namespace App\Http\Requests;

use App\Models\Despesa;
use Illuminate\Foundation\Http\FormRequest;

class StoreDespesaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Despesa::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /*$despesa = $this->route('despesa');
        $despesaId = $despesa instanceof Despesa? $despesa->id : (int)$despesa;*/
        return [
            'descricao' => 'required|string',
            'valor' => 'required|numeric|min:0',
            'cartao_id' => ['required', 'int', 'exists:cartaos,id']
        ];
    }

    public function messages():array
    {
        return [
            'descricao.required' => 'A despesa deve ter uma descrição.',
            'valor.min' => 'O valor deve ser maior que 0.'
        ];
    }

    public function returnDados(): array
    {
        return $this->validated();
    }
}
