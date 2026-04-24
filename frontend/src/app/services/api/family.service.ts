import { Injectable, Injector } from '@angular/core';
import { Observable } from 'rxjs';
import { BaseApiService, ApiResponse } from './base-api.service';

export interface Family {
  id: string | number;
  uuid?: string;
  restaurant_id: string | number;
  name: string;
  active: boolean;
  created_at?: string;
  updated_at?: string;
}

@Injectable({
  providedIn: 'root',
})
export class FamilyService extends BaseApiService {
  private endpoint = '/families';

  constructor(protected override injector: Injector) {
    super(injector);
  }

  getFamilies(): Observable<ApiResponse> {
    return this.httpCall(this.endpoint, null, 'get');
  }

  createFamily(data: any): Observable<ApiResponse> {
    return this.httpCall(this.endpoint, data, 'post');
  }

  updateFamily(id: string, data: any): Observable<ApiResponse> {
    return this.httpCall(`${this.endpoint}/${id}`, data, 'put');
  }

  deleteFamily(id: string): Observable<ApiResponse> {
    return this.httpCall(`${this.endpoint}/${id}`, null, 'delete');
  }
}