/**
 * Foster Venue static data constants
 */

// ─── Filter Chip Definitions ────────────────────────────────────

export const TYPE_CHIPS = [
    { value: '',           icon: 'bi bi-grid',        label: 'All Types' },
    { value: 'restaurant', icon: 'bi bi-cup-hot',     label: 'venue.type.restaurant' },
    { value: 'shelter',    icon: 'bi bi-house-heart',  label: 'venue.type.shelter' },
    { value: 'pet_store',  icon: 'bi bi-bag',          label: 'venue.type.pet_store' },
    { value: 'hair_salon', icon: 'bi bi-scissors',     label: 'venue.type.hair_salon' },
    { value: 'board_game', icon: 'bi bi-dice-5',       label: 'venue.type.board_game' },
    { value: 'vet_clinic', icon: 'bi bi-heart-pulse',  label: 'venue.type.vet_clinic' },
] as const

export const PET_CHIPS = [
    { value: '',    label: 'All Pet Types' },
    { value: 'cat', label: 'venue.pet_type.cat' },
    { value: 'dog', label: 'venue.pet_type.dog' },
] as const

