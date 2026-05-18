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
import { Order } from '../../../infrastructure/order.service';
import { CurrentOrderFacade } from '../../../application/current-order.facade';
import { Family } from '../../../../catalog/domain/family.model';
import {
  PaymentModalComponent,
  PaymentResult,
} from '../components/payment-modal/payment-modal.component';
import { PrebillModalComponent } from '../components/prebill-modal/prebill-modal.component';

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
    PaymentModalComponent,
    PrebillModalComponent,
  ],
})
export class OrdersComponent implements OnInit {
  orderId: string | null = null;
  currentOrder: Order | null = null;

  families: Family[] = [];

  products: Product[] = [];
  orderLines: CurrentOrderLine[] = [];

  user: any = null;
  isLoading = false;

  isSentToKitchen = false;
  sentLineIds: string[] = [];

  showPaymentModal = false;

  showPrebillModal = false;

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

    this.currentOrderFacade.loadCurrentOrderPage(this.orderId).subscribe({
      next: ({ currentOrder, products, orderLines, families }) => {
        this.currentOrder = currentOrder;
        this.products = products;
        this.orderLines = orderLines;
        this.families = families;
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
        this.getEditableOrderLines(),
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
  getEditableOrderLines(): CurrentOrderLine[] {
    if (!this.isSentToKitchen) {
      return this.orderLines;
    }

    return this.orderLines.filter(
      (line) => !this.sentLineIds.includes(line.id),
    );
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

    this.showPaymentModal = true;
  }

  confirmPayment(payment: PaymentResult): void {
    console.log('PAYMENT', payment);

    if (!this.currentOrder || !this.user) {
      return;
    }

    this.currentOrderFacade
      .checkoutOrder(this.currentOrder.id, this.user)
      .subscribe({
        next: () => {
          this.showPaymentModal = false;
          this.router.navigate(['/tpv/tables']);
        },
        error: (error: unknown) => {
          console.log('ERROR checkout', error);
        },
      });
  }

  getOrderTotal(): number {
    return this.orderLines.reduce((total, line) => {
      return total + line.price * line.quantity;
    }, 0);
  }

  sendToKitchen(): void {
    if (this.orderLines.length === 0) {
      return;
    }

    this.isSentToKitchen = true;
    this.sentLineIds = this.orderLines.map((line) => line.id);
  }
  openPrebill(): void {
    if (this.orderLines.length === 0) {
      return;
    }

    this.showPrebillModal = true;
  }
}
