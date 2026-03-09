<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ManualCaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'role' => 'required|in:owner,adopter',
            'pet_name' => 'required|string|max:50',
            'pet_type' => 'required|in:dog,cat',
            'pet_image' => 'nullable|image|max:5120',
            'counterpart_id' => 'nullable|exists:users,id',
            'tracking_config' => 'nullable|array',
            'tracking_config.frequency' => 'nullable|in:weekly,monthly,quarterly',
        ];
    }

    public function messages(): array
    {
        return [
            'role.required' => '請選擇您的身分',
            'role.in' => '身分必須為送養人或領養人',
            'pet_name.required' => '請輸入寵物名稱',
            'pet_name.max' => '寵物名稱不可超過 50 個字',
            'pet_type.required' => '請選擇寵物類型',
            'pet_type.in' => '寵物類型必須為狗或貓',
            'pet_image.image' => '請上傳有效的圖片檔案',
            'pet_image.max' => '圖片大小不可超過 5MB',
            'counterpart_id.exists' => '指定的使用者不存在',
            'tracking_config.frequency.in' => '追蹤頻率必須為：每週、每月或每季',
        ];
    }
}
