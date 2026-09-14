<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'child_id' => ['required', 'exists:children,id'],
            'question' => ['required', 'string', 'min:2', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'question.required' => 'Vui lòng nhập câu hỏi của bé.',
            'question.max' => 'Câu hỏi không được vượt quá 1000 ký tự.',
        ];
    }
}
