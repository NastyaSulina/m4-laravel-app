<?php

namespace App\Http\Requests\Userzone;

use Illuminate\Foundation\Http\FormRequest;

class AccommodationRequest extends FormRequest
{
    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'source_url' => ['required', 'url:http,https', 'max:2048'],
            'address' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:considering,selected,rejected'],
            'property_type' => ['required', 'in:apartment,house,room,hotel'],
            'note' => ['nullable', 'string', 'max:5000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:'.(config('media-library.max_file_size') / 1024)],
        ];
    }
}
