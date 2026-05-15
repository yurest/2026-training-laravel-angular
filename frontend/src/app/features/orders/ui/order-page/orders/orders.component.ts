import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, Router } from '@angular/router';
import { IonContent } from '@ionic/angular/standalone';
import { Product } from '../../../../catalog/domain/product.model';
import { ProductService } from '../../../../catalog/infrastructure/product.service';
import { OrderProductsComponent } from '../components/order-products/order-products.component';
import { OrderService, Order } from '../../../infrastructure/order.service';
import { OrderLineService } from '../../../infrastructure/order-line.service';
import { AuthService } from '../../../../identity/infrastructure/auth.service';
import {
  CurrentOrderLine,
  OrderSummaryComponent,
} from '../components/order-summary/order-summary.component';
import { extractArrayFromResponse } from '../../../../../shared/helpers/api-response.helper';
import { CurrentOrderFacade } from '../../../application/current-order.facade';

@Component({
  selector: 'app-orders',
  standalone: true,
  templateUrl: './orders.component.html',
  styleUrls: ['./orders.component.scss'],
  imports: [
    CommonModule,
    IonContent,
    OrderSummaryComponent,
    OrderProductsComponent,
  ],
})
export class OrdersComponent implements OnInit {
  orderId: string | null = null;
  currentOrder: Order | null = null;

  products: Product[] = [];
  orderLines: CurrentOrderLine[] = [];

  user: any = null;
  isLoading = false;

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private productService: ProductService,
    private orderService: OrderService,
    private orderLineService: OrderLineService,
    private authService: AuthService,
    private currentOrderFacade: CurrentOrderFacade,
  ) {}

  ngOnInit(): void {
    this.orderId = this.route.snapshot.paramMap.get('id');
    this.user = this.authService.getUser();

    this.loadPageData();
  }

  loadPageData(): void {
    if (!this.orderId) {
      return;
    }

    this.isLoading = true;

    this.currentOrderFacade.loadPageData(this.orderId).subscribe({
      next: ({ orderResponse, productsResponse, orderLinesResponse }: any) => {
        this.currentOrder = orderResponse;

        const products = extractArrayFromResponse<Product>(
          productsResponse,
          'products',
        );
        this.products = products.filter((product: Product) => product.active);

        const allOrderLines = extractArrayFromResponse<any>(
          orderLinesResponse,
          'order_lines',
        );

        this.orderLines = allOrderLines
          .filter(
            (line: any) =>
              String(line.order_id) === String(this.currentOrder?.id),
          )
          .map((line: any) => {
            const product = this.products.find(
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

        this.isLoading = false;
      },
      error: (error: unknown) => {
        console.log('ERROR loading order page', error);
        this.isLoading = false;
      },
    });
  }

  addProduct(product: Product): void {
    if (!this.currentOrder || !this.user) {
      return;
    }

    const existingLine = this.orderLines.find(
      (line) => String(line.product_id) === String(product.id),
    );

    if (existingLine) {
      this.updateLineQuantity(existingLine, existingLine.quantity + 1);
      return;
    }

    this.orderLineService
      .createOrderLine({
        restaurant_id: this.user.restaurant_id,
        order_id: this.currentOrder.id,
        product_id: product.id,
        user_id: this.user.id,
        quantity: 1,
        price: product.price,
        tax_percentage: 21,
      })
      .subscribe({
        next: (response: any) => {
          this.orderLines.push({
            id: response.id,
            product_id: product.id,
            name: product.name,
            price: product.price,
            quantity: 1,
            tax_percentage: 21,
          });
        },
        error: (error: unknown) => {
          console.log('ERROR creating order line', error);
        },
      });
  }

  increaseLine(line: CurrentOrderLine): void {
    this.updateLineQuantity(line, line.quantity + 1);
  }

  decreaseLine(line: CurrentOrderLine): void {
    if (line.quantity <= 1) {
      this.deleteLine(line);
      return;
    }

    this.updateLineQuantity(line, line.quantity - 1);
  }

  updateLineQuantity(line: CurrentOrderLine, quantity: number): void {
    this.orderLineService
      .updateOrderLine(line.id, {
        quantity,
        price: line.price,
      })
      .subscribe({
        next: () => {
          line.quantity = quantity;
        },
        error: (error: unknown) => {
          console.log('ERROR updating order line', error);
        },
      });
  }

  deleteLine(line: CurrentOrderLine): void {
    this.orderLineService.deleteOrderLine(line.id).subscribe({
      next: () => {
        this.orderLines = this.orderLines.filter((item) => item.id !== line.id);
      },
      error: (error: unknown) => {
        console.log('ERROR deleting order line', error);
      },
    });
  }

  checkout(): void {
    if (!this.currentOrder || !this.user || this.orderLines.length === 0) {
      return;
    }

    this.orderService
      .checkoutOrder(this.currentOrder.id, {
        restaurant_id: this.user.restaurant_id,
        user_id: this.user.id,
      })
      .subscribe({
        next: (response: any) => {
          console.log('SALE CREATED', response);
          this.router.navigate(['/tpv/tables']);
        },
        error: (error: unknown) => {
          console.log('ERROR checkout', error);
        },
      });
  }
}