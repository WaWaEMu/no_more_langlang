<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdoptionCaseRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'pet_id' => 'required|exists:pets,id',
            'adopter_id' => 'required|exists:users,id',
            'application_id' => 'required|exists:adoption_applications,id',
            'tracking_config' => 'nullable|array',
            'tracking_config.frequency' => 'nullable|in:weekly,monthly,quarterly,none'
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'pet_id.required' => '寵物 ID 為必填項目',
            'pet_id.exists' => '指定的寵物不存在',
            'adopter_id.required' => '認養人 ID 為必填項目',
            'adopter_id.exists' => '指定的認養人不存在',
            'application_id.required' => '申請 ID 為必填項目',
            'application_id.exists' => '指定的申請不存在',
            'tracking_config.frequency.in' => '追蹤頻率必須為：每週、每月、每季或不追蹤'
        ];
    }
}
