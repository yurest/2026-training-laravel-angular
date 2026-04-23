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
  private endpoint = '/family';

  constructor(protected override injector: Injector) {
    super(injector);
  }

  getFamilies(): Observable<ApiResponse> {
    return this.httpCall(this.endpoint, null, 'get');
  }
}