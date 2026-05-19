import { Injectable } from '@angular/core';
import { forkJoin, Observable } from 'rxjs';

import { ProductService } from '../../catalog/infrastructure/product.service';
import { OrderService } from '../infrastructure/order.service';
import { OrderLineService } from '../infrastructure/order-line.service';
import { Product } from '../../catalog/domain/product.model';
import { CurrentOrderLine } from '../ui/order-page/components/order-summary/order-summary.component';
import { map } from 'rxjs/operators';
import { Order } from '../infrastructure/order.service';
import { extractArrayFromResponse } from '../../../shared/helpers/api-response.helper';
import { FamilyService } from '../../catalog/infrastructure/family.service';
import { Family } from '../../catalog/domain/family.model';

@Injectable({
  providedIn: 'root',
})
export class CurrentOrderFacade {
  constructor(
    private productService: ProductService,
    private orderService: OrderService,
    private orderLineService: OrderLineService,
    private familyService: FamilyService,
  ) {}

  loadPageData(orderId: string): Observable<any> {
    return forkJoin({
      orderResponse: this.orderService.getOrder(orderId),
      productsResponse: this.productService.getProducts(),
      orderLinesResponse: this.orderLineService.getOrderLines(),
      familiesResponse: this.familyService.getFamilies(),
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

  deleteLine(lineId: string) {
    return this.orderLineService.deleteOrderLine(lineId);
  }

  checkoutOrder(orderId: string, user: any) {
    return this.orderService.checkoutOrder(orderId, {
      restaurant_id: user.restaurant_id,
      user_id: user.id,
    });
  }

  loadCurrentOrderPage(orderId: string) {
    return this.loadPageData(orderId).pipe(
      map(
        ({
          orderResponse,
          productsResponse,
          orderLinesResponse,
          familiesResponse,
        }: any) => {
          const currentOrder: Order = orderResponse;
          
          const families = extractArrayFromResponse<Family>(
            familiesResponse,
            'families',
          ).filter((family) => family.active);

          const products = extractArrayFromResponse<Product>(
            productsResponse,
            'products',
          ).filter((product) => {
            const familyIsActive = families.some(
              (family) => String(family.id) === String(product.family_id),
            );

            return product.active && product.stock > 0 && familyIsActive;
          });

          const allOrderLines = extractArrayFromResponse<any>(
            orderLinesResponse,
            'order_lines',
          );

          const orderLines: CurrentOrderLine[] = allOrderLines
            .filter(
              (line: any) => String(line.order_id) === String(currentOrder.id),
            )
            .map((line: any) => {
              const product = products.find(
                (item) => String(item.id) === String(line.product_id),
              );

              return {
                id: line.id,
                product_id: line.product_id,
                name: product?.name ?? 'Producto',
                price: line.price,
                quantity: line.quantity,
                tax_percentage: line.tax_percentage,
              };
            });

          return {
            currentOrder,
            products,
            families,
            orderLines,
          };
        },
      ),
    );
  }
}
