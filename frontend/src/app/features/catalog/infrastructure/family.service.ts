import { Injectable, Injector } from '@angular/core';
import { Observable } from 'rxjs';
import { BaseApiService, ApiResponse } from '../../../shared/data-access/base-api.service';
import {
  CreateFamilyPayload,
  UpdateFamilyPayload,
} from '../domain/family.model';

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

  createFamily(data: CreateFamilyPayload): Observable<ApiResponse> {
    return this.httpCall(this.endpoint, data, 'post');
  }

  updateFamily(id: string, data: UpdateFamilyPayload): Observable<ApiResponse> {
    return this.httpCall(`${this.endpoint}/${id}`, data, 'put');
  }

  deleteFamily(id: string): Observable<ApiResponse> {
    return this.httpCall(`${this.endpoint}/${id}`, null, 'delete');
  }
}