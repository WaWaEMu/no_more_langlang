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
            'pet_images' => 'nullable|array|max:3',
            'pet_images.*' => 'image|max:5120',
            'gender' => 'required|in:male,female',
            'age' => 'required|string|max:10',
            'color' => 'required|string|max:20',
            'fur_type' => 'required|string|in:短毛,長毛',
            'is_neuter' => 'required|boolean',
            'is_stray' => 'required|boolean',
            'city' => 'required|string|max:10',
            'town' => 'required|string|max:10',
            'counterpart_id' => 'nullable|exists:users,id',
            'tracking_config' => 'nullable|array',
            'tracking_config.frequency' => 'nullable|in:weekly,monthly,quarterly,none',
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
            'gender.required' => '請選擇寵物性別',
            'gender.in' => '性別必須為男生或女生',
            'age.required' => '請選擇寵物年紀',
            'color.required' => '請選擇寵物花紋',
            'fur_type.required' => '請選擇毛型',
            'fur_type.in' => '毛型必須為短毛或長毛',
            'is_neuter.required' => '請選擇結紮狀態',
            'is_stray.required' => '請選擇是否為浪浪',
            'city.required' => '請選擇所在縣市',
            'town.required' => '請選擇鄉鎮區',
            'pet_images.max' => '最多只能上傳 3 張圖片',
            'pet_images.*.image' => '請上傳有效的圖片檔案',
            'pet_images.*.max' => '每張圖片大小不可超過 5MB',
            'pet_images.*.uploaded' => '圖片上傳失敗，請檢查檔案大小或網路連線',
            'counterpart_id.exists' => '指定的使用者不存在',
            'tracking_config.frequency.in' => '追蹤頻率必須為：每週、每月、每季或暫不追蹤',
        ];
    }

    public function attributes(): array
    {
        return [
            'pet_name' => '寵物名稱',
            'pet_type' => '寵物類型',
            'pet_images' => '寵物照片',
            'gender' => '性別',
            'age' => '年紀',
            'color' => '花紋',
            'fur_type' => '毛型',
            'is_neuter' => '結紮狀態',
            'is_stray' => '是否為浪浪',
            'city' => '所在縣市',
            'town' => '鄉鎮區',
        ];
    }
}
