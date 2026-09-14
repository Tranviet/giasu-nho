<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChildRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'grade' => ['required', 'integer', 'min:1', 'max:5'],
            'avatar' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'grade.min' => 'Khối lớp từ 1 đến 5.',
            'grade.max' => 'Khối lớp từ 1 đến 5.',
        ];
    }
}
