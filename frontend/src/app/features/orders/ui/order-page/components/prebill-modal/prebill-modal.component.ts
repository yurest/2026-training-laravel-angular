import { CommonModule } from '@angular/common';
import { Component, EventEmitter, Input, Output } from '@angular/core';
import { IonButton, IonCard, IonCardContent } from '@ionic/angular/standalone';

export interface PrebillLine {
  name: string;
  quantity: number;
  price: number;
  tax_percentage?: number;
}

@Component({
  selector: 'app-prebill-modal',
  standalone: true,
  templateUrl: './prebill-modal.component.html',
  styleUrls: ['./prebill-modal.component.scss'],
  imports: [CommonModule, IonCard, IonCardContent, IonButton],
})
export class PrebillModalComponent {
  @Input() orderLines: PrebillLine[] = [];
  @Input() tableName = '';
  @Input() orderId: string | number | null = null;
  @Input() openedAt: string | null = null;

  @Output() closed = new EventEmitter<void>();

  getTotal(): number {
    return this.getGroupedLines().reduce((total, line) => {
      return total + line.price * line.quantity;
    }, 0);
  }

  getLineTotal(line: PrebillLine): number {
    return line.price * line.quantity;
  }

  printPrebill(): void {
    window.print();
  }
  getBaseAmount(): number {
    return this.getGroupedLines().reduce((total, line) => {
      const tax = line.tax_percentage ?? 21;
      const lineTotal = line.price * line.quantity;
      return total + Math.round(lineTotal / (1 + tax / 100));
    }, 0);
  }

  getTaxAmount(): number {
    return this.getTotal() - this.getBaseAmount();
  }

  getGroupedLines(): PrebillLine[] {
    const groupedLines: PrebillLine[] = [];

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
  formatDate(date: string | null): string {
    if (!date) {
      return '';
    }

    return new Date(date).toLocaleString('es-ES');
  }
}
