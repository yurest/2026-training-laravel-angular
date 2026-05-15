import { Injectable, Injector } from '@angular/core';
import { Observable } from 'rxjs';
import { BaseApiService, ApiResponse } from '../../../shared/data-access/base-api.service';
import {
  CreateProductPayload,
  UpdateProductPayload,
} from '../domain/product.model';

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

  createProduct(product: CreateProductPayload): Observable<ApiResponse> {
    return this.httpCall(this.endpoint, product, 'post');
  }

  updateProduct(
    id: string,
    product: UpdateProductPayload,
  ): Observable<ApiResponse> {
    return this.httpCall(`${this.endpoint}/${id}`, product, 'put');
  }

  deleteProduct(id: string): Observable<ApiResponse> {
    return this.httpCall(`${this.endpoint}/${id}`, null, 'delete');
  }
}