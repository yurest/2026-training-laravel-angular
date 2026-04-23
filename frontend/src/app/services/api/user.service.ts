import { Injectable, Injector } from '@angular/core';
import { Observable } from 'rxjs';
import { BaseApiService, ApiResponse } from './base-api.service';

export interface User {
  id: string | number;
  uuid?: string;
  restaurant_id: string | number;
  role: string;
  image_src?: string | null;
  name: string;
  email: string;
  pin?: string;
  created_at?: string;
  updated_at?: string;
}

@Injectable({
  providedIn: 'root',
})
export class UserService extends BaseApiService {
  private endpoint = '/users';

  constructor(protected override injector: Injector) {
    super(injector);
  }

  getUsers(): Observable<ApiResponse> {
    return this.httpCall(this.endpoint, null, 'get');
  }

  createUser(user: any): Observable<ApiResponse> {
    return this.httpCall(this.endpoint, user, 'post');
  }

  updateUser(id: string, user: any): Observable<ApiResponse> {
    return this.httpCall(`${this.endpoint}/${id}`, user, 'put');
  }

  deleteUser(id: string): Observable<ApiResponse> {
    return this.httpCall(`${this.endpoint}/${id}`, null, 'delete');
  }
}