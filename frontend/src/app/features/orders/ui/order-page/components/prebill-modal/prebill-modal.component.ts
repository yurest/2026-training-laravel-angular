import { CommonModule } from '@angular/common';
import { Component, EventEmitter, Input, Output } from '@angular/core';
import {
  IonButton,
  IonCard,
  IonCardContent,
} from '@ionic/angular/standalone';

export interface PrebillLine {
  name: string;
  quantity: number;
  price: number;
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

  @Output() closed = new EventEmitter<void>();

  getTotal(): number {
    return this.orderLines.reduce((total, line) => {
      return total + line.price * line.quantity;
    }, 0);
  }

  getLineTotal(line: PrebillLine): number {
    return line.price * line.quantity;
  }

  printPrebill(): void {
    window.print();
  }
}