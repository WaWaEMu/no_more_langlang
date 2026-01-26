export interface OptionItem {
  value: string | boolean
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
export interface PetFiltersInter {
  city: OptionItem
  color: OptionItem
  fur_type: OptionItem
  gender: OptionItem
  age: OptionItem
  is_neuter: OptionItem
  status: OptionItem
}
