<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HomeworkCheckRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'child_id' => ['required', 'exists:children,id'],
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'], // max 10MB
            'note' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'image.required' => 'Vui lòng tải lên ảnh bài tập.',
            'image.image' => 'Tệp tải lên phải là hình ảnh hợp lệ.',
            'image.max' => 'Dung lượng ảnh tối đa là 10MB.',
        ];
    }
}
