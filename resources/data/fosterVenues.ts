/**
 * Foster Venue static data constants
 */

// ─── Filter Chip Definitions ────────────────────────────────────

export const TYPE_CHIPS = [
    { value: '',                icon: 'bi bi-grid',        label: 'All Types' },
    { value: 'restaurant_cafe', icon: 'bi bi-cup-hot',     label: 'venue.type.restaurant_cafe' },
    { value: 'pet_grooming',    icon: 'bi bi-stars',       label: 'venue.type.pet_grooming' },
    { value: 'pet_hotel',       icon: 'bi bi-moon-stars',  label: 'venue.type.pet_hotel' },
    { value: 'pet_supplies',    icon: 'bi bi-bag',         label: 'venue.type.pet_supplies' },
    { value: 'hair_salon',      icon: 'bi bi-scissors',    label: 'venue.type.hair_salon' },
    { value: 'vet_clinic',      icon: 'bi bi-heart-pulse', label: 'venue.type.vet_clinic' },
    { value: 'public_shelter',  icon: 'bi bi-bank',        label: 'venue.type.public_shelter' },
    { value: 'private_shelter', icon: 'bi bi-house-heart', label: 'venue.type.private_shelter' },
    { value: 'leisure_hybrid',  icon: 'bi bi-dice-5',      label: 'venue.type.leisure_hybrid' },
] as const

export const PET_CHIPS = [
    { value: '',    label: 'All Pet Types' },
    { value: 'cat', label: 'venue.pet_type.cat' },
    { value: 'dog', label: 'venue.pet_type.dog' },
] as const

