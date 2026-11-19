<?php

namespace App\Http\Requests\Userzone;

use Illuminate\Foundation\Http\FormRequest;

class CriterionRequest extends FormRequest
{
    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'is_required' => ['boolean'],
        ];
    }
}
