import { Injectable, Injector } from '@angular/core';
import { Observable } from 'rxjs';
import { BaseApiService, ApiResponse } from '../../../shared/data-access/base-api.service';

export interface Zone {
  id: string;
  uuid?: string;
  numeric_id: number;
  restaurant_id: string | number;
  name: string;
  created_at?: string;
  updated_at?: string;
  deleted_at?: string | null;
}

@Injectable({
  providedIn: 'root',
})
export class ZoneService extends BaseApiService {
  private endpoint = '/zones';

  constructor(protected override injector: Injector) {
    super(injector);
  }

  getZones(): Observable<ApiResponse> {
    return this.httpCall(this.endpoint, null, 'get');
  }

  createZone(data: any): Observable<ApiResponse> {
    return this.httpCall(this.endpoint, data, 'post');
  }

  updateZone(id: string, data: any): Observable<ApiResponse> {
    return this.httpCall(`${this.endpoint}/${id}`, data, 'put');
  }

  deleteZone(id: string): Observable<ApiResponse> {
    return this.httpCall(`${this.endpoint}/${id}`, null, 'delete');
  }
}