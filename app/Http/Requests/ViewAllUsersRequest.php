<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class ViewAllUsersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('viewAny', User::class);
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
