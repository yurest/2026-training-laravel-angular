import { Injectable, Injector } from '@angular/core';
import { Observable } from 'rxjs';
import { BaseApiService, ApiResponse } from './base-api.service';

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

@Injectable({
  providedIn: 'root',
})
export class ProductService extends BaseApiService {
  private endpoint = '/products';

  constructor(protected override injector: Injector) {
    super(injector);
  }

  getProducts(): Observable<ApiResponse> {
    return this.httpCall(this.endpoint, null, 'get');
  }

  createProduct(product: any): Observable<ApiResponse> {
    return this.httpCall(this.endpoint, product, 'post');
  }

  updateProduct(id: string, product: any): Observable<ApiResponse> {
    return this.httpCall(`${this.endpoint}/${id}`, product, 'put');
  }

  deleteProduct(id: string): Observable<ApiResponse> {
    return this.httpCall(`${this.endpoint}/${id}`, null, 'delete');
  }
}