import { Injectable, Injector } from '@angular/core';
import { Observable } from 'rxjs';
import { BaseApiService, ApiResponse } from './base-api.service';

export interface Tax {
  id: string | number;
  uuid?: string;
  restaurant_id: string | number;
  name: string;
  percentage: number;
  created_at?: string;
  updated_at?: string;
}

@Injectable({
  providedIn: 'root',
})
export class TaxService extends BaseApiService {
  private endpoint = '/taxes';

  constructor(protected override injector: Injector) {
    super(injector);
  }

  getTaxes(): Observable<ApiResponse> {
    return this.httpCall(this.endpoint, null, 'get');
  }

  createTax(data: any): Observable<ApiResponse> {
    return this.httpCall(this.endpoint, data, 'post');
  }

  updateTax(id: string, data: any): Observable<ApiResponse> {
    return this.httpCall(`${this.endpoint}/${id}`, data, 'put');
  }

  deleteTax(id: string): Observable<ApiResponse> {
    return this.httpCall(`${this.endpoint}/${id}`, null, 'delete');
  }
}