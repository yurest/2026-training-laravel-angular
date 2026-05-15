import { Component, computed, EventEmitter, inject, Input, OnChanges, Output, SimpleChanges } from '@angular/core';
import { takeUntil } from 'rxjs/operators';
import { Subject } from 'rxjs';
import { AllergenCode, ProductItem } from '../../../services/product.service';
import { ProductRow } from '../../../pages/core/gestion/facades/gestion-products.facade';
import { ALLERGEN_CATALOG } from './allergen-catalog';
import { ProductModifiersFacade } from './facades/product-modifiers.facade';

export type ProductModifierTab = 'allergens' | 'variants' | 'extras' | 'accompaniments' | 'notes';

interface TabItem {
  key: ProductModifierTab;
  label: string;
  description: string;
}

@Component({
  selector: 'app-product-modifiers-modal',
  standalone: true,
  imports: [],
  templateUrl: './product-modifiers-modal.component.html',
  styleUrls: ['./product-modifiers-modal.component.scss'],
  providers: [ProductModifiersFacade],
})
export class ProductModifiersModalComponent implements OnChanges {
  @Input() isOpen = false;
  @Input() product: ProductRow | null = null;
  @Output() closeModal = new EventEmitter<void>();
  @Output() saved = new EventEmitter<ProductItem>();

  protected readonly facade = inject(ProductModifiersFacade);
  private readonly destroy$ = new Subject<void>();

  public activeTab: ProductModifierTab = 'allergens';

  public readonly selectedAllergens = computed(() => this.facade.selectedAllergens());
  public readonly isSaving = computed(() => this.facade.isSaving());
  public readonly error = computed(() => this.facade.error());
  public readonly hasChanges = computed(() => this.facade.hasChanges());

  public readonly tabs: TabItem[] = [
    { key: 'allergens', label: 'Alérgenos', description: 'Marca los 14 alérgenos oficiales presentes en este producto.' },
    { key: 'variants', label: 'Variantes', description: 'Tamaños/formatos con precio y stock propio (caña, tanque, copa, botella).' },
    { key: 'extras', label: 'Extras', description: 'Extras opcionales que suman al precio (queso, bacon...).' },
    { key: 'accompaniments', label: 'Acompañamientos', description: 'Opciones obligatorias sin coste (patata, arroz, ensalada).' },
    { key: 'notes', label: 'Notas', description: 'Notas libres que el camarero puede añadir al hacer la comanda.' },
  ];

  public readonly allergenCatalog = ALLERGEN_CATALOG;

  public ngOnChanges(changes: SimpleChanges): void {
    if (changes['product']) {
      this.facade.setProduct(this.product);
    }

    if (changes['isOpen'] && !this.isOpen) {
      this.facade.setError(null);
    }
  }

  public ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }

  public onClose(): void {
    this.closeModal.emit();
  }

  public selectTab(tab: ProductModifierTab): void {
    this.activeTab = tab;
  }

  public get currentTab(): TabItem {
    return this.tabs.find((t) => t.key === this.activeTab) ?? this.tabs[0];
  }

  public toggleAllergen(code: AllergenCode): void {
    this.facade.toggleAllergen(code);
  }

  public isAllergenSelected(code: AllergenCode): boolean {
    return this.facade.isAllergenSelected(code);
  }

  public onSave(): void {
    this.facade
      .save()
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (updated) => {
          this.saved.emit(updated);
          this.closeModal.emit();
        },
        error: () => {
          // El facade ya capturó el mensaje en `error`. El template lo muestra.
        },
      });
  }
}
