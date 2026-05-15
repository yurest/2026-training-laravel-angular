import { Injectable } from '@angular/core';
import { forkJoin, Observable } from 'rxjs';

import { ProductService } from '../../catalog/infrastructure/product.service';
import { OrderService } from '../infrastructure/order.service';
import { OrderLineService } from '../infrastructure/order-line.service';

@Injectable({
  providedIn: 'root',
})
export class CurrentOrderFacade {
  constructor(
    private productService: ProductService,
    private orderService: OrderService,
    private orderLineService: OrderLineService,
  ) {}

  loadPageData(orderId: string): Observable<any> {
    return forkJoin({
      orderResponse: this.orderService.getOrder(orderId),
      productsResponse: this.productService.getProducts(),
      orderLinesResponse: this.orderLineService.getOrderLines(),
    });
  }
}