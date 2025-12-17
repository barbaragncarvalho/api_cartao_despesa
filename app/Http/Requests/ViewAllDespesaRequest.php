<?php

namespace App\Http\Requests;

use App\Models\Despesa;
use Illuminate\Foundation\Http\FormRequest;

class ViewAllDespesaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('viewAny', Despesa::class);
    }

    public function rules(): array
    {
        return [
            'paginate' => 'sometimes|int|min:1|max:100',
        ];
    }

    public function getPaginate(): int
    {
        return (int) $this->query('paginate', 10);
    }
}
