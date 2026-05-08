import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { BaseApiService } from '../core/services/api/base-api.service';

export interface ProductItem {
  id: string;
  family_id: string;
  tax_id: string;
  image_src: string | null;
  name: string;
  price: number;
  stock: number;
  active: boolean;
  created_at: string;
  updated_at: string;
}

interface CreateProductPayload {
  family_id: string;
  tax_id: string;
  image_src?: string | null;
  name: string;
  price: number;
  stock: number;
  active?: boolean;
}

interface UpdateProductPayload {
  family_id: string;
  tax_id: string;
  image_src?: string | null;
  name: string;
  price: number;
  stock: number;
  active: boolean;
}

@Injectable({
  providedIn: 'root',
})
export class ProductService extends BaseApiService {
  protected override readonly defaultErrorMessage = 'No se pudo completar la peticion de productos.';

  public listProducts(): Observable<ProductItem[]> {
    return this.get<ProductItem[]>('/admin/products');
  }

  public createProduct(payload: CreateProductPayload): Observable<ProductItem> {
    return this.post<ProductItem>('/admin/products', payload);
  }

  public updateProduct(id: string, payload: UpdateProductPayload): Observable<ProductItem> {
    return this.put<ProductItem>(`/admin/products/${id}`, payload);
  }

  public deleteProduct(id: string): Observable<void> {
    return this.delete<void>(`/admin/products/${id}`);
  }

  public activateProduct(id: string): Observable<ProductItem> {
    return this.patch<ProductItem>(`/admin/products/${id}/activate`);
  }

  public deactivateProduct(id: string): Observable<ProductItem> {
    return this.patch<ProductItem>(`/admin/products/${id}/deactivate`);
  }
}
