<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'status'      => 'required|in:active,inactive',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'   => 'Kategoriya nomi majburiy',
            'status.required' => 'Status majburiy',
        ];
    }
}
