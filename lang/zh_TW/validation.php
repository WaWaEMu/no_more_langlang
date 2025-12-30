<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => '必須接受 :attribute。',
    'accepted_if' => '當 :other 為 :value 時，必須接受 :attribute。',
    'active_url' => ':attribute 並非有效的網址。',
    'after' => ':attribute 必須要晚於 :date。',
    'after_or_equal' => ':attribute 必須要等於或晚於 :date。',
    'alpha' => ':attribute 只能以字母組成。',
    'alpha_dash' => ':attribute 只能以字母、數字、破折號及底線組成。',
    'alpha_num' => ':attribute 只能以字母及數字組成。',
    'array' => ':attribute 必須為陣列。',
    'ascii' => ':attribute 只能包含單字節的字母數字字符和符號。',
    'before' => ':attribute 必須要早於 :date。',
    'before_or_equal' => ':attribute 必須要等於或早於 :date。',
    'between' => [
        'array' => ':attribute 必須有 :min 至 :max 個項目。',
        'file' => ':attribute 必須介於 :min 至 :max KB 之間。',
        'numeric' => ':attribute 必須介於 :min 至 :max 之間。',
        'string' => ':attribute 必須介於 :min 至 :max 個字元之間。',
    ],
    'boolean' => ':attribute 必須為布林值。',
    'can' => ':attribute 包含未經授權的值。',
    'confirmed' => ':attribute 確認欄位不一致。',
    'current_password' => '密碼不正確。',
    'date' => ':attribute 並非有效的日期。',
    'date_equals' => ':attribute 必須等於 :date。',
    'date_format' => ':attribute 不符合 :format 的格式。',
    'decimal' => ':attribute 必須有 :decimal 位小數。',
    'declined' => ':attribute 必須拒絕。',
    'declined_if' => '當 :other 為 :value 時，必須拒絕 :attribute。',
    'different' => ':attribute 與 :other 必須不同。',
    'digits' => ':attribute 必須是 :digits 位數字。',
    'digits_between' => ':attribute 必須介於 :min 至 :max 位數字之間。',
    'dimensions' => ':attribute 圖片尺寸不正確。',
    'distinct' => ':attribute 已經存在。',
    'doesnt_end_with' => ':attribute 的結尾不能是以下之一：:values。',
    'doesnt_start_with' => ':attribute 的開頭不能是以下之一：:values。',
    'email' => ':attribute 必須是有效的電子郵件地址。',
    'ends_with' => ':attribute 的結尾必須是以下之一：:values。',
    'enum' => '所選的 :attribute 無效。',
    'exists' => '所選的 :attribute 無效。',
    'file' => ':attribute 必須是檔案。',
    'filled' => ':attribute 不能留空。',
    'gt' => [
        'array' => ':attribute 必須多於 :value 個項目。',
        'file' => ':attribute 必須大於 :value KB。',
        'numeric' => ':attribute 必須大於 :value。',
        'string' => ':attribute 必須多於 :value 個字元。',
    ],
    'gte' => [
        'array' => ':attribute 必須多於或等於 :value 個項目。',
        'file' => ':attribute 必須大於或等於 :value KB。',
        'numeric' => ':attribute 必須大於或等於 :value。',
        'string' => ':attribute 必須多於或等於 :value 個字元。',
    ],
    'image' => ':attribute 必須是圖片。',
    'in' => '所選的 :attribute 無效。',
    'in_array' => ':attribute 不存在於 :other。',
    'integer' => ':attribute 必須是整數。',
    'ip' => ':attribute 必須是有效的 IP 地址。',
    'ipv4' => ':attribute 必須是有效的 IPv4 地址。',
    'ipv6' => ':attribute 必須是有效的 IPv6 地址。',
    'json' => ':attribute 必須是正確的 JSON 字串。',
    'lowercase' => ':attribute 必須小寫。',
    'lt' => [
        'array' => ':attribute 必須少於 :value 個項目。',
        'file' => ':attribute 必須小於 :value KB。',
        'numeric' => ':attribute 必須小於 :value。',
        'string' => ':attribute 必須少於 :value 個字元。',
    ],
    'lte' => [
        'array' => ':attribute 必須少於或等於 :value 個項目。',
        'file' => ':attribute 必須小於或等於 :value KB。',
        'numeric' => ':attribute 必須小於或等於 :value。',
        'string' => ':attribute 必須少於或等於 :value 個字元。',
    ],
    'mac_address' => ':attribute 必須是有效的 MAC 地址。',
    'max' => [
        'array' => ':attribute 不能多於 :max 個項目。',
        'file' => ':attribute 不能大於 :max KB。',
        'numeric' => ':attribute 不能大於 :max。',
        'string' => ':attribute 不能多於 :max 個字元。',
    ],
    'max_digits' => ':attribute 不能多於 :max 位數字。',
    'mimes' => ':attribute 必須為 :values 的檔案。',
    'mimetypes' => ':attribute 必須為 :values 的檔案。',
    'min' => [
        'array' => ':attribute 至少有 :min 個項目。',
        'file' => ':attribute 至少為 :min KB。',
        'numeric' => ':attribute 至少為 :min。',
        'string' => ':attribute 至少為 :min 個字元。',
    ],
    'min_digits' => ':attribute 至少有 :min 位數字。',
    'missing' => ':attribute 必須缺失。',
    'missing_if' => '當 :other 為 :value 時，:attribute 必須缺失。',
    'missing_unless' => '除非 :other 為 :value，否則 :attribute 必須缺失。',
    'missing_with' => '當 :values 存在時，:attribute 必須缺失。',
    'missing_with_all' => '當 :values 存在時，:attribute 必須缺失。',
    'multiple_of' => ':attribute 必須是 :value 的倍數。',
    'not_in' => '所選的 :attribute 無效。',
    'not_regex' => ':attribute 的格式錯誤。',
    'numeric' => ':attribute 必須為數字。',
    'password' => [
        'letters' => ':attribute 必須包含至少一個字母。',
        'mixed' => ':attribute 必須包含至少一個大寫字母和一個小寫字母。',
        'numbers' => ':attribute 必須包含至少一個數字。',
        'symbols' => ':attribute 必須包含至少一個符號。',
        'uncompromised' => '給定的 :attribute 出現在數據洩漏中。請選擇不同的 :attribute。',
    ],
    'present' => ':attribute 必須存在。',
    'prohibited' => ':attribute 欄位被禁止。',
    'prohibited_if' => '當 :other 為 :value 時，:attribute 欄位被禁止。',
    'prohibited_unless' => '除非 :other 在 :values 中，否則 :attribute 欄位被禁止。',
    'prohibits' => ':attribute 欄位禁止 :other 存在。',
    'regex' => ':attribute 的格式錯誤。',
    'required' => ':attribute 不能留空。',
    'required_array_keys' => ':attribute 必須包含以下項目：:values。',
    'required_if' => '當 :other 為 :value 時，:attribute 不能留空。',
    'required_if_accepted' => '當 :other 被接受時，:attribute 不能留空。',
    'required_unless' => '除非 :other 為 :values，否則 :attribute 不能留空。',
    'required_with' => '當 :values 出現時，:attribute 不能留空。',
    'required_with_all' => '當 :values 出現時，:attribute 不能留空。',
    'required_without' => '當 :values 留空時，:attribute 不能留空。',
    'required_without_all' => '當 :values 都不出現時，:attribute 不能留空。',
    'same' => ':attribute 與 :other 必須相同。',
    'size' => [
        'array' => ':attribute 必須包含 :size 個項目。',
        'file' => ':attribute 的大小必須是 :size KB。',
        'numeric' => ':attribute 的大小必須是 :size。',
        'string' => ':attribute 必須是 :size 個字元。',
    ],
    'starts_with' => ':attribute 的開頭必須是以下之一：:values。',
    'string' => ':attribute 必須是字串。',
    'timezone' => ':attribute 必須是有效的時區。',
    'unique' => ':attribute 已經被使用。',
    'uploaded' => ':attribute 上傳失敗。',
    'uppercase' => ':attribute 必須大寫。',
    'url' => ':attribute 的格式錯誤。',
    'ulid' => ':attribute 必須是有效的 ULID。',
    'uuid' => ':attribute 必須是有效的 UUID。',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'email' => '電子信箱',
        'password' => '密碼',
        'name' => '用戶暱稱',
    ],

];
