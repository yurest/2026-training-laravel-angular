import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { BaseApiService } from '../core/services/api/base-api.service';

export interface AdminRestaurantItem {
  uuid: string;
  name: string;
  legal_name: string;
  tax_id: string;
  email: string;
  users: number;
  zones: number;
  products: number;
  has_open_cash_session: boolean;
}

export interface AdminRestaurantUser {
  uuid: string;
  name: string;
  email: string;
  role: string;
}

interface AdminRestaurantsResponse {
  data: AdminRestaurantItem[];
}

interface AdminRestaurantUsersResponse {
  users: AdminRestaurantUser[];
}

interface SelectRestaurantContextResponse {
  success: boolean;
  restaurant_id: string;
  name: string;
}

interface UpdateAdminRestaurantPayload {
  name: string;
  legal_name?: string;
  tax_id?: string;
  email: string;
  password?: string;
}

interface CreateRestaurantUserPayload {
  name: string;
  email: string;
  password: string;
  role: string;
  pin?: string;
}

interface UpdateRestaurantUserPayload {
  name?: string;
  email?: string;
  password?: string;
  role?: string;
  pin?: string;
}

@Injectable({
  providedIn: 'root',
})
export class RestaurantService extends BaseApiService {
  protected override readonly defaultErrorMessage = 'No se pudo completar la peticion de restaurantes.';

  public getAdminRestaurants(): Observable<AdminRestaurantsResponse> {
    return this.get<AdminRestaurantsResponse>('/admin/restaurants');
  }

  public selectAdminRestaurantContext(restaurantId: string): Observable<SelectRestaurantContextResponse> {
    return this.post<SelectRestaurantContextResponse>('/admin/context/restaurant', { restaurant_id: restaurantId });
  }

  public updateAdminRestaurant(restaurantId: string, payload: UpdateAdminRestaurantPayload): Observable<void> {
    return this.put<void>(`/admin/restaurants/${restaurantId}`, payload);
  }

  public getRestaurantUsers(restaurantUuid: string): Observable<AdminRestaurantUsersResponse> {
    return this.get<AdminRestaurantUsersResponse>(`/admin/restaurants/${restaurantUuid}/users`);
  }

  public createRestaurantUser(restaurantUuid: string, payload: CreateRestaurantUserPayload): Observable<AdminRestaurantUser> {
    return this.post<AdminRestaurantUser>(`/admin/restaurants/${restaurantUuid}/users`, payload);
  }

  public updateRestaurantUser(restaurantUuid: string, userUuid: string, payload: UpdateRestaurantUserPayload): Observable<void> {
    return this.put<void>(`/admin/restaurants/${restaurantUuid}/users/${userUuid}`, payload);
  }

  public deleteRestaurantUser(restaurantUuid: string, userUuid: string): Observable<void> {
    return this.delete<void>(`/admin/restaurants/${restaurantUuid}/users/${userUuid}`);
  }
}
