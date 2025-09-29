import type { OptionInter } from '@/types/option'

export const isStrayOptions: OptionInter = [
    { value: "", label: "請選擇", disabled: true },
    { value: true, label: "是" },
    { value: false, label: "否" }
]

export const furOptions: OptionInter = [
    { value: "", label: "請選擇毛型", disabled: true },
    { value: "短毛", label: "短毛" },
    { value: "長毛", label: "長毛" },
]

export const genderOptions: OptionInter = [
    { value: "", label: "請選擇性別", disabled: true },
    { value: "male", label: "男生" },
    { value: "female", label: "女生" },
]

export const ageOptions: OptionInter = [
    { value: "", label: "請選擇年紀範圍", disabled: true },
    { value: "0-1", label: "0 ~ 1歲" },
    { value: "1-4", label: "1 ~ 4歲" },
    { value: "4-7", label: "4 ~ 7歲" },
    { value: "7-10", label: "7 ~ 10歲" },
    { value: "10-13", label: "10 ~ 13歲" },
    { value: "13-16", label: "13 ~ 16歲" },
    { value: "16+", label: "16歲 ~" }
]

export const isNeuterOptions: OptionInter = [
    { value: "", label: "請選擇結紮狀態", disabled: true },
    { value: true, label: "已結紮" },
    { value: false, label: "未結紮" }
]

export default {
    isStrayOptions,
    furOptions,
    genderOptions,
    ageOptions,
    isNeuterOptions
}
