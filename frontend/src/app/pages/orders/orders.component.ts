import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute } from '@angular/router';
import {
  IonContent,
  IonCard,
  IonCardContent,
  IonButton,
} from '@ionic/angular/standalone';

interface ProductItem {
  id: number;
  name: string;
  price: number; // en céntimos
}

interface OrderLine {
  id: number;
  name: string;
  price: number; // en céntimos
  quantity: number;
}

@Component({
  selector: 'app-orders',
  standalone: true,
  templateUrl: './orders.component.html',
  styleUrls: ['./orders.component.scss'],
  imports: [CommonModule, IonContent, IonCard, IonCardContent, IonButton],
})
export class OrdersComponent implements OnInit {
  tableId: string | null = null;

  products: ProductItem[] = [
    { id: 1, name: 'Coca-Cola', price: 350 },
    { id: 2, name: 'Agua', price: 200 },
    { id: 3, name: 'Hamburguesa', price: 990 },
    { id: 4, name: 'Patatas', price: 450 },
  ];

  orderLines: OrderLine[] = [];

  constructor(private route: ActivatedRoute) {}

  ngOnInit() {
    this.tableId = this.route.snapshot.paramMap.get('id');
  }

  addProduct(product: ProductItem): void {
    const existingLine = this.orderLines.find((line) => line.id === product.id);

    if (existingLine) {
      existingLine.quantity++;
      return;
    }

    this.orderLines.push({
      id: product.id,
      name: product.name,
      price: product.price,
      quantity: 1,
    });
  }

  getLineTotal(line: OrderLine): number {
    return line.price * line.quantity; // sigue en céntimos
  }

  getOrderTotal(): number {
    return this.orderLines.reduce(
      (total, line) => total + this.getLineTotal(line),
      0
    );
  }
}