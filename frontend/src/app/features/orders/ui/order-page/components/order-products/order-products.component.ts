import { CommonModule } from '@angular/common';
import { Component, EventEmitter, Input, Output } from '@angular/core';
import {
  IonCard,
  IonCardContent,
} from '@ionic/angular/standalone';
import { Product } from '../../../../../catalog/domain/product.model';

@Component({
  selector: 'app-order-products',
  standalone: true,
  templateUrl: './order-products.component.html',
  styleUrls: ['./order-products.component.scss'],
  imports: [
    CommonModule,
    IonCard,
    IonCardContent,
  ],
})
export class OrderProductsComponent {
  @Input() products: Product[] = [];

  @Output() productSelected = new EventEmitter<Product>();
}