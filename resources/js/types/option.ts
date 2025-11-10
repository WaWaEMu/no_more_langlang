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

// ------------------------------
// Definitions related to filters
// ------------------------------
export type FilterValue = string | boolean

export interface PetFiltersInter {
  city: FilterValue
  color: FilterValue
  fur_type: FilterValue
  gender: FilterValue
  age: FilterValue
  is_neuter: FilterValue
}
