<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DiaryCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => '請輸入回覆內容',
            'content.max' => '回覆最多 500 字',
        ];
    }
}
