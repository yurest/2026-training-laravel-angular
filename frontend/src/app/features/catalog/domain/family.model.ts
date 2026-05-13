export interface Family {
  id: string | number;
  uuid?: string;
  restaurant_id: string | number;
  name: string;
  active: boolean;
  created_at?: string;
  updated_at?: string;
}

export interface CreateFamilyPayload {
  restaurant_id: string | number;
  name: string;
  active: boolean;
}

export type UpdateFamilyPayload = Partial<CreateFamilyPayload>;