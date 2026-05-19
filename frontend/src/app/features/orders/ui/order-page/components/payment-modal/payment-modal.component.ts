import { CommonModule } from '@angular/common';
import { Component, EventEmitter, Input, Output } from '@angular/core';
import {
  IonButton,
  IonCard,
  IonCardContent,
  IonInput,
} from '@ionic/angular/standalone';
import { FormsModule } from '@angular/forms';

export type PaymentMethod = 'cash' | 'card';

export interface PaymentLine {
  method: PaymentMethod;
  amount: number;
  receivedAmount?: number;
  changeAmount?: number;
}

export interface PaymentResult {
  payments: PaymentLine[];
  totalPaid: number;
  changeAmount: number;
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

  cashReceived = 0;
  cardAmount = 0;

  payments: PaymentLine[] = [];
  quickCashAmounts = [5, 10, 20, 50, 100];

  selectMethod(method: PaymentMethod): void {
    this.selectedMethod = method;

    if (method === 'cash') {
      this.cashReceived = this.getPendingAmount() / 100;
    }

    if (method === 'card') {
      this.cardAmount = this.getPendingAmount() / 100;
    }
  }

  setQuickCashAmount(amount: number): void {
    this.cashReceived = amount;
  }

  setExactAmount(): void {
    this.cashReceived = this.getPendingAmount() / 100;
  }

  addPayment(): void {
    if (this.selectedMethod === 'cash') {
      this.addCashPayment();
      return;
    }

    this.addCardPayment();
  }

  addCashPayment(): void {
    const receivedAmount = Math.round(this.cashReceived * 100);

    if (receivedAmount <= 0) {
      return;
    }

    const pendingAmount = this.getPendingAmount();
    const paymentAmount = Math.min(receivedAmount, pendingAmount);
    const changeAmount = Math.max(receivedAmount - pendingAmount, 0);

    this.payments.push({
      method: 'cash',
      amount: paymentAmount,
      receivedAmount,
      changeAmount,
    });

    this.cashReceived = 0;
  }

  addCardPayment(): void {
    const amount = Math.round(this.cardAmount * 100);

    if (amount <= 0 || amount > this.getPendingAmount()) {
      return;
    }

    this.payments.push({
      method: 'card',
      amount,
    });

    this.cardAmount = 0;
  }

  removePayment(index: number): void {
    this.payments.splice(index, 1);
  }

  confirmPayment(): void {
    if (!this.isPaymentComplete()) {
      return;
    }

    this.paymentConfirmed.emit({
      payments: this.payments,
      totalPaid: this.getPaidAmount(),
      changeAmount: this.getChangeAmount(),
    });
  }

  getTotalInEuros(): number {
    return this.total / 100;
  }

  getPaidAmount(): number {
    return this.payments.reduce((total, payment) => {
      return total + payment.amount;
    }, 0);
  }

  getPendingAmount(): number {
    return Math.max(this.total - this.getPaidAmount(), 0);
  }

  getChangeAmount(): number {
    return this.payments.reduce((total, payment) => {
      return total + (payment.changeAmount ?? 0);
    }, 0);
  }

  isPaymentComplete(): boolean {
    return this.total > 0 && this.getPendingAmount() === 0;
  }

  fixNegativeAmounts(): void {
    if (this.cashReceived < 0) {
      this.cashReceived = 0;
    }

    if (this.cardAmount < 0) {
      this.cardAmount = 0;
    }
  }
}
