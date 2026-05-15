import { Injectable } from '@angular/core';
import { forkJoin, Observable } from 'rxjs';

import { ProductService } from '../../catalog/infrastructure/product.service';
import { OrderService } from '../infrastructure/order.service';
import { OrderLineService } from '../infrastructure/order-line.service';
import { Product } from '../../catalog/domain/product.model';
import { CurrentOrderLine } from '../ui/order-page/components/order-summary/order-summary.component';

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
  addProductToOrder(
    product: Product,
    orderId: string | number,
    user: any,
    orderLines: CurrentOrderLine[],
  ): Observable<CurrentOrderLine | null> {
    const existingLine = orderLines.find(
      (line) => String(line.product_id) === String(product.id),
    );

    if (existingLine) {
      return this.updateLineQuantity(existingLine, existingLine.quantity + 1);
    }

    return new Observable<CurrentOrderLine>((observer) => {
      this.orderLineService
        .createOrderLine({
          restaurant_id: user.restaurant_id,
          order_id: orderId,
          product_id: product.id,
          user_id: user.id,
          quantity: 1,
          price: product.price,
          tax_percentage: 21,
        })
        .subscribe({
          next: (response: any) => {
            observer.next({
              id: response.id,
              product_id: product.id,
              name: product.name,
              price: product.price,
              quantity: 1,
              tax_percentage: 21,
            });
            observer.complete();
          },
          error: (error) => observer.error(error),
        });
    });
  }

  updateLineQuantity(
    line: CurrentOrderLine,
    quantity: number,
  ): Observable<CurrentOrderLine | null> {
    return new Observable<CurrentOrderLine>((observer) => {
      this.orderLineService
        .updateOrderLine(line.id, {
          quantity,
          price: line.price,
        })
        .subscribe({
          next: () => {
            observer.next({
              ...line,
              quantity,
            });
            observer.complete();
          },
          error: (error) => observer.error(error),
        });
    });
  }
}
