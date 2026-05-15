import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, Router } from '@angular/router';
import { IonContent } from '@ionic/angular/standalone';
import { Product } from '../../../../catalog/domain/product.model';
import { OrderProductsComponent } from '../components/order-products/order-products.component';
import { AuthService } from '../../../../identity/infrastructure/auth.service';
import {
  CurrentOrderLine,
  OrderSummaryComponent,
} from '../components/order-summary/order-summary.component';
import { extractArrayFromResponse } from '../../../../../shared/helpers/api-response.helper';
import { Order } from '../../../infrastructure/order.service';
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

    this.currentOrderFacade
      .addProductToOrder(
        product,
        this.currentOrder.id,
        this.user,
        this.orderLines,
      )
      .subscribe({
        next: (line) => {
          if (!line) {
            return;
          }

          const existingLine = this.orderLines.find(
            (item) => String(item.id) === String(line.id),
          );

          if (existingLine) {
            existingLine.quantity = line.quantity;
            return;
          }

          this.orderLines.push(line);
        },
        error: (error: unknown) => {
          console.log('ERROR adding product to order', error);
        },
      });
  }

  increaseLine(line: CurrentOrderLine): void {
    this.changeLineQuantity(line, line.quantity + 1);
  }

  decreaseLine(line: CurrentOrderLine): void {
    if (line.quantity <= 1) {
      this.deleteLine(line);
      return;
    }

    this.changeLineQuantity(line, line.quantity - 1);
  }

  changeLineQuantity(line: CurrentOrderLine, quantity: number): void {
    this.currentOrderFacade.updateLineQuantity(line, quantity).subscribe({
      next: (updatedLine) => {
        const existingLine = this.orderLines.find(
          (item) => item.id === updatedLine?.id,
        );

        if (existingLine && updatedLine) {
          existingLine.quantity = updatedLine.quantity;
        }
      },
      error: (error: unknown) => {
        console.log('ERROR updating order line', error);
      },
    });
  }

  deleteLine(line: CurrentOrderLine): void {
    this.currentOrderFacade.deleteLine(line.id).subscribe({
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

    this.currentOrderFacade
      .checkoutOrder(this.currentOrder.id, this.user)
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
