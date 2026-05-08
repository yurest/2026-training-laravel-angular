import { computed, inject, Injectable, Signal, signal } from '@angular/core';
import { firstValueFrom } from 'rxjs';
import { TaxItem, TaxService } from '../../../../services/tax.service';

export interface TaxRow {
  uuid?: string;
  name: string;
  percentage: number;
}

export interface TaxFormData {
  name: string;
  percentage: number;
}

const EMPTY_FORM: TaxFormData = { name: '', percentage: 10 };

export interface OperationResult {
  ok: boolean;
  error?: string;
  message?: string;
}

@Injectable()
export class GestionTaxesFacade {
  private readonly taxService = inject(TaxService);

  private readonly _taxes = signal<TaxRow[]>([]);
  private readonly _selectedIndex = signal<number>(-1);
  private readonly _formData = signal<TaxFormData>({ ...EMPTY_FORM });
  private readonly _isSaving = signal<boolean>(false);
  private readonly _isLoading = signal<boolean>(false);

  public readonly taxes: Signal<TaxRow[]> = this._taxes.asReadonly();
  public readonly selectedIndex: Signal<number> = this._selectedIndex.asReadonly();
  public readonly formData: Signal<TaxFormData> = this._formData.asReadonly();
  public readonly isSaving: Signal<boolean> = this._isSaving.asReadonly();
  public readonly isLoading: Signal<boolean> = this._isLoading.asReadonly();

  public readonly selectedTax: Signal<TaxRow | null> = computed(() => {
    const index = this._selectedIndex();
    const list = this._taxes();

    return index >= 0 && index < list.length ? list[index] : null;
  });

  public async load(): Promise<void> {
    this._isLoading.set(true);

    try {
      const taxesResponse = await firstValueFrom(this.taxService.listTaxes());
      const taxes = Array.isArray(taxesResponse) ? taxesResponse : (taxesResponse as any).items || [];
      const rows: TaxRow[] = taxes.map((tax: TaxItem) => ({
        uuid: tax.id,
        name: tax.name,
        percentage: tax.percentage,
      }));

      this._taxes.set(rows);
      this.syncFormFromIndex();
    } finally {
      this._isLoading.set(false);
    }
  }

  public clear(): void {
    this._taxes.set([]);
    this._selectedIndex.set(-1);
    this._formData.set({ ...EMPTY_FORM });
  }

  public select(index: number): void {
    this._selectedIndex.set(index);
    this.syncFormFromIndex();
  }

  public startCreate(): void {
    this._selectedIndex.set(-1);
    this._formData.set({ ...EMPTY_FORM });
  }

  public updateForm<K extends keyof TaxFormData>(key: K, value: TaxFormData[K]): void {
    this._formData.update((current) => ({ ...current, [key]: value }));
  }

  public setForm(data: TaxFormData): void {
    this._formData.set({ ...data });
  }

  public async deleteSelected(): Promise<OperationResult> {
    const tax = this.selectedTax();

    if (!tax?.uuid) {
      return { ok: false, error: 'No se puede eliminar: impuesto sin identificador.' };
    }

    try {
      await firstValueFrom(this.taxService.deleteTax(tax.uuid));
      const newList = this._taxes().filter((candidate) => candidate.uuid !== tax.uuid);
      this._taxes.set(newList);
      this._selectedIndex.set(newList.length > 0 ? 0 : -1);
      this.syncFormFromIndex();

      return { ok: true, message: `Impuesto "${tax.name}" eliminado.` };
    } catch (error) {
      return { ok: false, error: error instanceof Error ? error.message : 'No se pudo eliminar el impuesto.' };
    }
  }

  public async delete(uuid: string): Promise<OperationResult> {
    try {
      await firstValueFrom(this.taxService.deleteTax(uuid));
      const newList = this._taxes().filter((candidate) => candidate.uuid !== uuid);
      this._taxes.set(newList);
      const currentIndex = this._selectedIndex();
      this._selectedIndex.set(newList.length > 0 ? Math.min(currentIndex, newList.length - 1) : -1);
      this.syncFormFromIndex();

      return { ok: true, message: 'Impuesto eliminado.' };
    } catch (error) {
      return { ok: false, error: error instanceof Error ? error.message : 'No se pudo eliminar el impuesto.' };
    }
  }

  public async save(): Promise<OperationResult> {
    const form = this._formData();
    const name = form.name.trim();
    const percentage = Number(form.percentage);

    if (!name) {
      return { ok: false, error: 'Indica el nombre del impuesto.' };
    }

    if (isNaN(percentage) || percentage < 0 || percentage > 100) {
      return { ok: false, error: 'El porcentaje debe ser un número entre 0 y 100.' };
    }

    const selected = this.selectedTax();

    this._isSaving.set(true);

    try {
      if (selected?.uuid) {
        const updated = await firstValueFrom(this.taxService.updateTax(selected.uuid, { name, percentage }));

        this.replaceTax(selected.uuid, {
          uuid: updated.id,
          name: updated.name,
          percentage: updated.percentage,
        });

        this.syncFormFromIndex();

        return { ok: true, message: 'Impuesto actualizado.' };
      }

      const created = await firstValueFrom(this.taxService.createTax({ name, percentage }));

      const newRow: TaxRow = {
        uuid: created.id,
        name: created.name,
        percentage: created.percentage,
      };

      const newList = [...this._taxes(), newRow];
      this._taxes.set(newList);
      this._selectedIndex.set(newList.length - 1);
      this.syncFormFromIndex();

      return { ok: true, message: 'Impuesto creado.' };
    } catch (error) {
      return { ok: false, error: error instanceof Error ? error.message : 'No se pudo guardar el impuesto.' };
    } finally {
      this._isSaving.set(false);
    }
  }

  private replaceTax(uuid: string, replacement: TaxRow): void {
    this._taxes.update((current) => current.map((tax) => (tax.uuid === uuid ? replacement : tax)));
  }

  private syncFormFromIndex(): void {
    const tax = this.selectedTax();

    if (tax) {
      this._formData.set({ name: tax.name, percentage: tax.percentage });
    } else {
      this._formData.set({ ...EMPTY_FORM });
    }
  }
}
