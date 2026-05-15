import { computed, inject, Injectable, OnDestroy, Signal, signal } from '@angular/core';
import { Observable, Subject, throwError } from 'rxjs';
import { catchError, takeUntil, tap } from 'rxjs/operators';
import { AllergenCode, ProductItem, ProductService } from '../../../../services/product.service';
import { ProductRow } from '../../../../pages/core/gestion/facades/gestion-products.facade';

@Injectable()
export class ProductModifiersFacade implements OnDestroy {
  private readonly productService = inject(ProductService);
  private readonly destroy$ = new Subject<void>();

  // Signals privados — estado
  private readonly _product = signal<ProductRow | null>(null);
  private readonly _selectedAllergens = signal<AllergenCode[]>([]);
  private readonly _isSaving = signal<boolean>(false);
  private readonly _error = signal<string | null>(null);

  // Signals públicos — solo lectura
  public readonly product: Signal<ProductRow | null> = this._product.asReadonly();
  public readonly selectedAllergens: Signal<AllergenCode[]> = this._selectedAllergens.asReadonly();
  public readonly isSaving: Signal<boolean> = this._isSaving.asReadonly();
  public readonly error: Signal<string | null> = this._error.asReadonly();

  public readonly hasChanges: Signal<boolean> = computed(() => {
    const product = this._product();
    if (!product) {
      return false;
    }

    const current = this._selectedAllergens();
    const original = product.allergens;

    if (current.length !== original.length) {
      return true;
    }

    return !current.every((code) => original.includes(code));
  });

  // Setters explícitos
  public setProduct(product: ProductRow | null): void {
    this._product.set(product);
    this._selectedAllergens.set(product ? [...product.allergens] : []);
    this._error.set(null);
  }

  public setAllergens(codes: AllergenCode[]): void {
    this._selectedAllergens.set([...codes]);
  }

  public setError(value: string | null): void {
    this._error.set(value);
  }

  // Métodos de UI
  public toggleAllergen(code: AllergenCode): void {
    this._selectedAllergens.update((current) =>
      current.includes(code) ? current.filter((c) => c !== code) : [...current, code],
    );
  }

  public isAllergenSelected(code: AllergenCode): boolean {
    return this._selectedAllergens().includes(code);
  }

  public reset(): void {
    this._product.set(null);
    this._selectedAllergens.set([]);
    this._isSaving.set(false);
    this._error.set(null);
  }

  // Métodos de negocio
  public save(): Observable<ProductItem> {
    const product = this._product();

    if (!product?.uuid) {
      return throwError(() => new Error('Producto no válido para guardar modificadores.'));
    }

    this._isSaving.set(true);
    this._error.set(null);

    return this.productService
      .updateProduct(product.uuid, {
        name: product.name,
        family_id: product.family_id,
        tax_id: product.tax_id,
        price: product.price,
        stock: product.stock,
        active: product.active,
        allergens: this._selectedAllergens(),
      })
      .pipe(
        tap(() => this._isSaving.set(false)),
        catchError((err) => {
          this._isSaving.set(false);
          const message = err instanceof Error ? err.message : 'No se pudo guardar los modificadores.';
          this._error.set(message);

          return throwError(() => new Error(message));
        }),
        takeUntil(this.destroy$),
      );
  }

  public ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }
}
