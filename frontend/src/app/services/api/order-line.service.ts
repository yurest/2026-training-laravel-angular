import { Injectable, Injector } from '@angular/core';
import { Observable } from 'rxjs';
import { ApiResponse, BaseApiService } from './base-api.service';

export interface OrderLine {
  id: string;
  restaurant_id: string | number;
  order_id: string;
  product_id: string;
  user_id: string | number;
  quantity: number;
  price: number;
  tax_percentage: number;
  created_at?: string;
  updated_at?: string;
}

@Injectable({
  providedIn: 'root',
})
export class OrderLineService extends BaseApiService {
  private endpoint = '/order-lines';

  constructor(protected override injector: Injector) {
    super(injector);
  }

  getOrderLines(): Observable<ApiResponse> {
    return this.httpCall(this.endpoint, null, 'get');
  }

  createOrderLine(orderLine: any): Observable<ApiResponse> {
    return this.httpCall(this.endpoint, orderLine, 'post');
  }

  updateOrderLine(id: string, orderLine: any): Observable<ApiResponse> {
    return this.httpCall(`${this.endpoint}/${id}`, orderLine, 'put');
  }

  deleteOrderLine(id: string): Observable<ApiResponse> {
    return this.httpCall(`${this.endpoint}/${id}`, null, 'delete');
  }
}