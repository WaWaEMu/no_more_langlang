export interface OptionItem {
    value: string | number | boolean
    label: string
    disabled?: boolean
}

export interface OptionGroup {
  key: string
  items: OptionItem[]
}

export type OptionInter = OptionGroup
