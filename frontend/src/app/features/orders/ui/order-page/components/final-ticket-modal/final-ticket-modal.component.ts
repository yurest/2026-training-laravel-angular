import { CommonModule } from '@angular/common';
import { Component, EventEmitter, Input, Output } from '@angular/core';
import { IonButton, IonCard, IonCardContent } from '@ionic/angular/standalone';
import { PaymentLine } from '../payment-modal/payment-modal.component';

export interface FinalTicketLine {
  name: string;
  quantity: number;
  price: number;
  tax_percentage?: number;
}

@Component({
  selector: 'app-final-ticket-modal',
  standalone: true,
  templateUrl: './final-ticket-modal.component.html',
  styleUrls: ['./final-ticket-modal.component.scss'],
  imports: [CommonModule, IonCard, IonCardContent, IonButton],
})
export class FinalTicketModalComponent {
  @Input() orderLines: FinalTicketLine[] = [];
  @Input() payments: PaymentLine[] = [];
  @Input() tableName = '';
  @Input() openedAt: string | null = null;

  @Output() closed = new EventEmitter<void>();

  getGroupedLines(): FinalTicketLine[] {
    const groupedLines: FinalTicketLine[] = [];

    this.orderLines.forEach((line) => {
      const existingLine = groupedLines.find(
        (item) =>
          item.name === line.name &&
          item.price === line.price &&
          item.tax_percentage === line.tax_percentage,
      );

      if (existingLine) {
        existingLine.quantity += line.quantity;
        return;
      }

      groupedLines.push({ ...line });
    });

    return groupedLines;
  }

  getLineTotal(line: FinalTicketLine): number {
    return line.price * line.quantity;
  }

  getTotal(): number {
    return this.orderLines.reduce((total, line) => {
      return total + line.price * line.quantity;
    }, 0);
  }

  getBaseAmount(): number {
    return this.orderLines.reduce((total, line) => {
      const tax = line.tax_percentage ?? 21;
      const lineTotal = line.price * line.quantity;

      return total + Math.round(lineTotal / (1 + tax / 100));
    }, 0);
  }

  getTaxAmount(): number {
    return this.getTotal() - this.getBaseAmount();
  }

  getPaidAmount(): number {
    return this.payments.reduce((total, payment) => {
      return total + payment.amount;
    }, 0);
  }

  getChangeAmount(): number {
    return this.payments.reduce((total, payment) => {
      return total + (payment.changeAmount ?? 0);
    }, 0);
  }

  printTicket(): void {
    window.print();
  }

  formatDate(date: string | null): string {
    if (!date) {
      return '';
    }

    return new Date(date).toLocaleString('es-ES');
  }
}