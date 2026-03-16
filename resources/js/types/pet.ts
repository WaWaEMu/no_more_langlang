export interface PetFormInter {
  title: string;
  city: string;
  town: string;
  is_stray: boolean | null;
  type: string;
  color: string;
  fur_type: string;
  name: string;
  gender: string;
  age: string;
  is_neuter: boolean | null;
  sendable_city: string[];
  adoption_description: string;
  health_description: string;
  adoption_condition: string;
  status?: string;
  adoption_form_template_id?: number | null;
  blobs: Blob[];
}

export interface PetDetail {
  id: number
  pet_id: number
  adoption_description: string
  health_description: string
  adoption_condition: string
  created_at: string
  updated_at: string
}

export interface PetInter {
  id: number
  title: string
  name: string
  type: string
  color: string
  fur_type: string
  gender: string
  age: string
  is_neuter: boolean | 0 | 1
  is_stray: boolean | 0 | 1
  city: string
  town: string
  sendable_cities: string[]
  detail: PetDetail
  images: Array<{ path: string }>
  user: { id: number; name: string }
  created_at: string
  updated_at: string
  status: string
  is_favorite?: boolean
  adoption_case?: {
    id: number
    adopter: {
      id: number
      name: string
    }
    owner?: {
      id: number
      name: string
    }
    started_at: string
    status: string
    tracking_config?: {
      frequency: string
      tracking_day?: number
      tracking_start_month?: number
    }
    next_report_due_at?: string
    last_report_at?: string
  }
}

export interface TrackingReport {
  id: number
  adoption_case_id: number
  reporter_id: number
  reporter?: {
    id: number
    name: string
  }
  content: string
  images?: string[]
  reported_at: string
  created_at: string
}

export interface PetColorMapInter {
  [petType: string]: string[]
}
