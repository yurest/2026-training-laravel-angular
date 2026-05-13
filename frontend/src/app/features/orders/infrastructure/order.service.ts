import { Injectable, Injector } from '@angular/core';
import { Observable } from 'rxjs';
import { ApiResponse, BaseApiService } from '../../../services/api/base-api.service';

export interface Order {
  id: string;
  restaurant_id: string | number;
  status: string;
  table_id: string | number;
  opened_by_user_id: string | number;
  closed_by_user_id?: string | number | null;
  diners: number;
  opened_at?: string;
  closed_at?: string | null;
  created_at?: string;
  updated_at?: string;
}

@Injectable({
  providedIn: 'root',
})
export class OrderService extends BaseApiService {
  private endpoint = '/orders';

  constructor(protected override injector: Injector) {
    super(injector);
  }

  getOrders(): Observable<ApiResponse> {
    return this.httpCall(this.endpoint, null, 'get');
  }

  getOrder(id: string): Observable<ApiResponse> {
    return this.httpCall(`${this.endpoint}/${id}`, null, 'get');
  }

  createOrder(order: any): Observable<ApiResponse> {
    return this.httpCall(this.endpoint, order, 'post');
  }

  updateOrder(id: string, order: any): Observable<ApiResponse> {
    return this.httpCall(`${this.endpoint}/${id}`, order, 'put');
  }

  checkoutOrder(id: string, payload: any): Observable<ApiResponse> {
    return this.httpCall(`${this.endpoint}/${id}/checkout`, payload, 'post');
  }
  getOpenOrders(restaurantId: string | number): Observable<ApiResponse> {
    return this.httpCall(
      `${this.endpoint}/open?restaurant_id=${restaurantId}`,
      null,
      'get',
    );
  }
  
}
