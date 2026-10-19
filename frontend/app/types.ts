export interface Package {
  id: number
  name: string
  slug: string
  category: string
  tagline: string | null
  description: string | null
  price_cents: number
  duration_weeks: number
  sessions_per_week: number
  level: string
  mode: string
  features: string[] | null
  program: { title: string; text: string }[] | null
  color: 'pink' | 'magenta' | 'black'
  is_featured: boolean
  is_active: boolean
  sort: number
}

export interface Session {
  id: number
  title: string
  description: string | null
  starts_at: string
  ends_at: string
  location: string
  capacity: number
  spots_left: number
  package: Pick<Package, 'id' | 'name' | 'slug' | 'color'> | null
  is_booked: boolean
  can_book: boolean
}

export interface Order {
  id: number
  status: 'pending' | 'active' | 'completed' | 'cancelled'
  price_cents: number
  notes: string | null
  starts_on: string | null
  ends_on: string | null
  created_at: string
  package: Pick<Package, 'id' | 'name' | 'slug' | 'color' | 'duration_weeks'>
  user?: { id: number; name: string; email: string; phone: string | null; status: string }
}
