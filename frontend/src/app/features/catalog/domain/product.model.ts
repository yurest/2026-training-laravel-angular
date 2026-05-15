export interface Product {
  id: string | number;
  uuid?: string;
  restaurant_id: string | number;
  family_id: string | number;
  tax_id: string | number;
  stock: number;
  image_src?: string | null;
  active: boolean;
  name: string;
  price: number; // en céntimos
  created_at?: string;
  updated_at?: string;
}

export interface CreateProductPayload {
  restaurant_id: string | number;
  family_id: string | number;
  tax_id: string | number;
  stock: number;
  image_src?: string | null;
  active: boolean;
  name: string;
  price: number;
}

export type UpdateProductPayload = Partial<CreateProductPayload>;