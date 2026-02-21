<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrackingReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'content' => 'required|string|max:1000',
            'images' => 'nullable|array|max:3',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120', // 5MB per image
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'content.required' => '請輸入回報內容',
            'content.max' => '回報內容不可超過 1000 字',
            'images.max' => '最多只能上傳 3 張照片',
            'images.*.image' => '上傳的檔案必須是圖片',
            'images.*.mimes' => '圖片格式僅支援 jpeg、png、jpg、webp',
            'images.*.max' => '每張圖片大小不可超過 5MB',
        ];
    }
}
