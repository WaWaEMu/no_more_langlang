<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DiaryEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'photos' => ['required', 'array', 'min:1', 'max:5'],
            'photos.*' => ['image', 'max:5120'], // 每張最大 5 MB
            'content' => ['nullable', 'string', 'max:200'],
            'location' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'photos.required' => '請上傳至少一張照片',
            'photos.min' => '請上傳至少一張照片',
            'photos.max' => '最多只能上傳 5 張照片',
            'photos.*.image' => '檔案必須是圖片格式',
            'photos.*.max' => '每張圖片大小不可超過 5 MB',
            'content.max' => '內容最多 200 字',
            'location.max' => '地點最多 50 字',
        ];
    }
}
