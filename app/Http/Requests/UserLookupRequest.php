<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserLookupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => '請輸入 Email',
            'email.email' => '請輸入正確的 Email 格式',
        ];
    }
}
