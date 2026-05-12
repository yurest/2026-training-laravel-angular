import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import {
  IonCard,
  IonCardContent,
  IonButton,
  IonItem,
  IonLabel,
  IonInput,
} from '@ionic/angular/standalone';
import { Tax, TaxService } from '../../../../services/api/tax.service';
import { AuthService } from '../../../../services/auth/auth.service';
import { AlertService } from '../../../../services/ui/alert.service';
import { extractBackendErrors } from '../../../../shared/api-error.util';

@Component({
  selector: 'app-taxes-settings',
  standalone: true,
  templateUrl: './taxes-settings.component.html',
  styleUrls: ['./taxes-settings.component.scss'],
  imports: [
    CommonModule,
    FormsModule,
    IonCard,
    IonCardContent,
    IonButton,
    IonItem,
    IonLabel,
    IonInput,
  ],
})
export class TaxesSettingsComponent implements OnInit {
  taxes: Tax[] = [];
  filteredTaxes: Tax[] = [];
  taxErrorMessages: string[] = [];
  taxSearchTerm = '';

  taxMode: 'create' | 'edit' = 'create';
  editingTax: Tax | null = null;

  createTaxForm = {
    name: '',
    percentage: 21,
  };

  editTaxForm = {
    name: '',
    percentage: 21,
  };

  constructor(
    private taxService: TaxService,
    private authService: AuthService,
    private alertService: AlertService,
  ) {}

  ngOnInit(): void {
    this.loadTaxes();
  }

  loadTaxes(): void {
    const restaurantId = this.authService.getUser()?.restaurant_id;

    this.taxService.getTaxes().subscribe({
      next: (response: any) => {
        const taxes = Array.isArray(response)
          ? response
          : Array.isArray(response.data)
            ? response.data
            : (response.tax ?? response.taxes ?? []);

        this.taxes = restaurantId
          ? taxes.filter(
              (tax: Tax) => String(tax.restaurant_id) === String(restaurantId),
            )
          : taxes;

        this.applyTaxFilter();
      },
      error: () => {
        this.taxErrorMessages = ['No se pudieron cargar los impuestos.'];
      },
    });
  }

  createTax(): void {
    const restaurantId = this.authService.getUser()?.restaurant_id;

    this.taxErrorMessages = [];

    const errors = this.validateCreateTaxForm(restaurantId);

    if (errors.length > 0) {
      this.taxErrorMessages = errors;
      return;
    }

    const payload = {
      name: this.createTaxForm.name.trim(),
      percentage: Number(this.createTaxForm.percentage),
      restaurant_id: restaurantId,
    };

    this.taxService.createTax(payload).subscribe({
      next: () => {
        this.resetCreateTaxForm();
        this.loadTaxes();
        this.alertService.showSuccess('Impuesto creado correctamente');
      },
      error: (error) => {
        this.taxErrorMessages = extractBackendErrors(error);
      },
    });
  }

  startEditTax(tax: Tax): void {
    this.taxMode = 'edit';
    this.editingTax = tax;
    this.taxErrorMessages = [];

    this.editTaxForm = {
      name: tax.name ?? '',
      percentage: Number(tax.percentage ?? 0),
    };
  }

  cancelEditTax(): void {
    this.taxMode = 'create';
    this.editingTax = null;
    this.taxErrorMessages = [];
    this.resetEditTaxForm();
  }

  saveEditTax(): void {
    if (!this.editingTax) {
      return;
    }

    this.taxErrorMessages = [];

    const errors = this.validateEditTaxForm();

    if (errors.length > 0) {
      this.taxErrorMessages = errors;
      return;
    }

    const payload = {
      name: this.editTaxForm.name.trim(),
      percentage: Number(this.editTaxForm.percentage),
    };

    const idToUpdate = String(this.editingTax.uuid ?? this.editingTax.id);

    this.taxService.updateTax(idToUpdate, payload).subscribe({
      next: () => {
        this.cancelEditTax();
        this.loadTaxes();
        this.alertService.showSuccess('Impuesto actualizado correctamente');
      },
      error: (error) => {
        this.taxErrorMessages = extractBackendErrors(error);
      },
    });
  }

  async deleteTax(tax: Tax): Promise<void> {
    await this.alertService.confirmDelete(
      'Eliminar impuesto',
      `¿Seguro que quieres eliminar "${tax.name}"?`,
      () => this.confirmDeleteTax(tax),
    );
  }

  confirmDeleteTax(tax: Tax): void {
    const idToDelete = String(tax.uuid ?? tax.id);

    this.taxService.deleteTax(idToDelete).subscribe({
      next: () => {
        this.loadTaxes();
      },
      error: () => {
        this.taxErrorMessages = ['No se pudo eliminar el impuesto.'];
      },
    });
  }

  onTaxSearchChange(): void {
    this.applyTaxFilter();
  }

  applyTaxFilter(): void {
    const term = this.taxSearchTerm.trim().toLowerCase();

    if (!term) {
      this.filteredTaxes = [...this.taxes];
      return;
    }

    this.filteredTaxes = this.taxes.filter((tax) => {
      const name = tax.name?.toLowerCase() ?? '';
      return name.includes(term);
    });
  }

  clearTaxSearch(): void {
    this.taxSearchTerm = '';
    this.applyTaxFilter();
  }

  resetCreateTaxForm(): void {
    this.createTaxForm = {
      name: '',
      percentage: 21,
    };
    this.taxErrorMessages = [];
  }

  resetEditTaxForm(): void {
    this.editTaxForm = {
      name: '',
      percentage: 21,
    };
  }

  private validateCreateTaxForm(
    restaurantId: string | number | undefined,
  ): string[] {
    const errors: string[] = [];

    if (!restaurantId) {
      errors.push('No se ha encontrado el restaurant_id.');
    }

    if (!this.createTaxForm.name.trim()) {
      errors.push('El nombre del impuesto es obligatorio.');
    }

    if (
      this.createTaxForm.percentage === null ||
      this.createTaxForm.percentage === undefined
    ) {
      errors.push('El porcentaje es obligatorio.');
    }

    if (
      isNaN(Number(this.createTaxForm.percentage)) ||
      Number(this.createTaxForm.percentage) < 0 ||
      Number(this.createTaxForm.percentage) > 100
    ) {
      errors.push('El porcentaje debe estar entre 0 y 100.');
    }

    return errors;
  }

  private validateEditTaxForm(): string[] {
    const errors: string[] = [];

    if (!this.editTaxForm.name.trim()) {
      errors.push('El nombre del impuesto es obligatorio.');
    }

    if (
      isNaN(Number(this.editTaxForm.percentage)) ||
      Number(this.editTaxForm.percentage) < 0 ||
      Number(this.editTaxForm.percentage) > 100
    ) {
      errors.push('El porcentaje debe estar entre 0 y 100.');
    }

    return errors;
  }
}