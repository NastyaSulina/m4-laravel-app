<?php

namespace App\Http\Requests\Userzone;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AccommodationCriteriaRequest extends FormRequest
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'criteria' => ['required', 'array'],
            'criteria.*' => ['array:id,is_met'],
            'criteria.*.id' => [
                'required', 'integer', 'distinct',
                Rule::in($this->route('selection')->criteria()->pluck('id')),
            ],
            'criteria.*.is_met' => ['present', 'nullable', 'boolean'],
        ];
    }
}
