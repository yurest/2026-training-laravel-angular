export interface Tax {
  id: string | number;
  uuid?: string;
  restaurant_id: string | number;
  name: string;
  percentage: number;
  created_at?: string;
  updated_at?: string;
}

export interface CreateTaxPayload {
  restaurant_id: string | number;
  name: string;
  percentage: number;
}

export type UpdateTaxPayload = Partial<CreateTaxPayload>;