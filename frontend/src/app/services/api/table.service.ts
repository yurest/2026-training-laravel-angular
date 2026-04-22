import { Injectable, Injector } from '@angular/core';
import { Observable } from 'rxjs';
import { BaseApiService, ApiResponse } from './base-api.service';

export interface TableItem {
  id: string | number;
  uuid: string;
  restaurant_id: string | number;
  zone_id: string | number;
  name: string;
  created_at?: string;
  updated_at?: string;
  deleted_at?: string | null;
  status?: 'free' | 'occupied';
}

@Injectable({
  providedIn: 'root',
})
export class TableService extends BaseApiService {
  private endpoint = '/tables';

  constructor(protected override injector: Injector) {
    super(injector);
  }

  getTables(): Observable<ApiResponse> {
    return this.httpCall(this.endpoint, null, 'get');
  }
}