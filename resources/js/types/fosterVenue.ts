export interface FosterVenueInter {
    id: number
    uuid: string
    name: string
    type: string[]
    primary_type_display_name: string | null
    description: string | null
    phone: string | null
    city: string
    district: string | null
    address: string
    latitude: number | null
    longitude: number | null
    rating: number | null
    user_rating_count: number | null
    business_hours: string[] | null
    website_url: string | null
    facebook_url: string | null
    instagram_url: string | null
    line_id: string | null
    pet_types: string[]
    services: string[]
    status: string
    business_status: string | null
    is_verified: boolean
    created_at: string
    updated_at: string
}

export interface FosterVenueFiltersInter {
    city: string
    district: string
    type: string
    pet_type: string
}
