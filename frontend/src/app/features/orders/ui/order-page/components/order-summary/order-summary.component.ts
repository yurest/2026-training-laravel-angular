import { CommonModule } from '@angular/common';
import { Component, EventEmitter, Input, Output } from '@angular/core';
import { IonButton } from '@ionic/angular/standalone';

export interface CurrentOrderLine {
  id: string;
  product_id: string | number;
  name: string;
  price: number;
  quantity: number;
  tax_percentage: number;
}

@Component({
  selector: 'app-order-summary',
  standalone: true,
  templateUrl: './order-summary.component.html',
  styleUrls: ['./order-summary.component.scss'],
  imports: [CommonModule, IonButton],
})
export class OrderSummaryComponent {
  @Input() orderLines: CurrentOrderLine[] = [];
  @Input() isSentToKitchen = false;
  @Input() sentLineIds: string[] = [];

  @Output() increaseLine = new EventEmitter<CurrentOrderLine>();
  @Output() decreaseLine = new EventEmitter<CurrentOrderLine>();
  @Output() deleteLine = new EventEmitter<CurrentOrderLine>();
  @Output() checkout = new EventEmitter<void>();
  @Output() sendToKitchen = new EventEmitter<void>();
  @Output() openPrebill = new EventEmitter<void>();

  getLineTotal(line: CurrentOrderLine): number {
    return line.price * line.quantity;
  }

  getOrderTotal(): number {
    return this.orderLines.reduce(
      (total, line) => total + this.getLineTotal(line),
      0,
    );
  }
  getPendingLinesCount(): number {
    return this.orderLines.filter((line) => !this.sentLineIds.includes(line.id))
      .length;
  }
}
