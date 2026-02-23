<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('employee')?->user_id;

        return [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $userId,
            'password' => $this->isMethod('POST') ? 'required|min:6' : 'nullable|min:6',
            'role'     => 'required|in:admin,manager,seller',
            'phone'    => 'nullable|string|max:20',
            'address'  => 'nullable|string|max:500',
            'salary'   => 'nullable|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'     => 'Ism majburiy',
            'email.required'    => 'Email majburiy',
            'email.unique'      => 'Bu email allaqachon mavjud',
            'password.required' => 'Parol majburiy',
            'password.min'      => 'Parol kamida 6 ta belgi bo\'lishi kerak',
            'role.required'     => 'Role tanlash majburiy',
        ];
    }
}
