import { CommonModule } from '@angular/common';
import { Component, EventEmitter, Input, Output } from '@angular/core';
import { IonCard, IonCardContent,IonButton } from '@ionic/angular/standalone';
import { Product } from '../../../../../catalog/domain/product.model';
import { Family } from '../../../../../catalog/domain/family.model';

@Component({
  selector: 'app-order-products',
  standalone: true,
  templateUrl: './order-products.component.html',
  styleUrls: ['./order-products.component.scss'],
  imports: [CommonModule, IonCard, IonCardContent, IonButton],
})
export class OrderProductsComponent {
  @Input() products: Product[] = [];
  @Input() families: Family[] = [];

  @Output() productSelected = new EventEmitter<Product>();

  selectedFamilyId: string | number | null = null;

  selectFamily(familyId: string | number): void {
    this.selectedFamilyId = familyId;
  }

  getFilteredProducts(): Product[] {
    if (!this.selectedFamilyId) {
      return [];
    }

    return this.products.filter(
      (product) => String(product.family_id) === String(this.selectedFamilyId),
    );
  }
}
