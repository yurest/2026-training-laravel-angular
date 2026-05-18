import { CommonModule } from '@angular/common';
import { Component, EventEmitter, Input, Output } from '@angular/core';
import {
  IonButton,
  IonCard,
  IonCardContent,
  IonInput,
} from '@ionic/angular/standalone';
import { FormsModule } from '@angular/forms';

export type PaymentMethod = 'cash' | 'card' | 'mixed';

export interface PaymentResult {
  method: PaymentMethod;
  cashAmount: number;
  cardAmount: number;
}

@Component({
  selector: 'app-payment-modal',
  standalone: true,
  templateUrl: './payment-modal.component.html',
  styleUrls: ['./payment-modal.component.scss'],
  imports: [
    CommonModule,
    FormsModule,
    IonCard,
    IonCardContent,
    IonButton,
    IonInput,
  ],
})
export class PaymentModalComponent {
  @Input() total = 0;

  @Output() paymentConfirmed = new EventEmitter<PaymentResult>();
  @Output() closed = new EventEmitter<void>();

  selectedMethod: PaymentMethod = 'cash';

  cashAmount = 0;
  cardAmount = 0;

  selectMethod(method: PaymentMethod): void {
    this.selectedMethod = method;

    if (method === 'cash') {
      this.cashAmount = this.total / 100;
      this.cardAmount = 0;
    }

    if (method === 'card') {
      this.cashAmount = 0;
      this.cardAmount = this.total / 100;
    }

    if (method === 'mixed') {
      this.cashAmount = 0;
      this.cardAmount = 0;
    }
  }

  confirmPayment(): void {
    if (!this.isPaymentValid()) {
      return;
    }

    this.paymentConfirmed.emit({
      method: this.selectedMethod,
      cashAmount: Math.round(this.cashAmount * 100),
      cardAmount: Math.round(this.cardAmount * 100),
    });
  }

  getTotalInEuros(): number {
    return this.total / 100;
  }
  getPaymentTotal(): number {
    return (
      Math.round(this.cashAmount * 100) + Math.round(this.cardAmount * 100)
    );
  }

  isPaymentValid(): boolean {
    if (this.selectedMethod === 'cash' || this.selectedMethod === 'card') {
      return this.total > 0;
    }

    if (this.selectedMethod === 'mixed') {
      return (
        this.cashAmount >= 0 &&
        this.cardAmount >= 0 &&
        this.getPaymentTotal() === this.total
      );
    }

    return false;
  }
  fixNegativeAmounts(): void {
    if (this.cashAmount < 0) {
      this.cashAmount = 0;
    }

    if (this.cardAmount < 0) {
      this.cardAmount = 0;
    }
  }
}
