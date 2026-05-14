import { TYPE_CHIPS } from '../../data/fosterVenues'

/** Icon map derived from TYPE_CHIPS to avoid duplication */
const typeIconMap: Record<string, string> = Object.fromEntries(
    TYPE_CHIPS.filter(c => c.value).map(c => [c.value, c.icon])
)

/**
 * Get the Bootstrap Icon class for a given venue type
 */
export function getTypeIcon(type: string): string {
    return typeIconMap[type] ?? 'bi bi-patch-question'
}
