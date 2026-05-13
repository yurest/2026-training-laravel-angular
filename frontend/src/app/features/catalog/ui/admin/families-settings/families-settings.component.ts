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
  IonCheckbox,
} from '@ionic/angular/standalone';
import { Family } from '../../../domain/family.model';
import { FamilyService } from '../../../infrastructure/family.service';
import { AuthService } from '../../../../../services/auth/auth.service';
import { AlertService } from '../../../../../shared/services/alert.service';
import { extractBackendErrors } from '../../../../../shared/helpers/extract-backend-errors.helper';

@Component({
  selector: 'app-families-settings',
  standalone: true,
  templateUrl: './families-settings.component.html',
  styleUrls: ['./families-settings.component.scss'],
  imports: [
    CommonModule,
    FormsModule,
    IonCard,
    IonCardContent,
    IonButton,
    IonItem,
    IonLabel,
    IonInput,
    IonCheckbox,
  ],
})
export class FamiliesSettingsComponent implements OnInit {
  families: Family[] = [];
  filteredFamilies: Family[] = [];
  familyErrorMessages: string[] = [];
  familySearchTerm = '';

  familyMode: 'create' | 'edit' = 'create';
  editingFamily: Family | null = null;

  createFamilyForm = {
    name: '',
    active: true,
  };

  editFamilyForm = {
    name: '',
    active: true,
  };

  constructor(
    private familyService: FamilyService,
    private authService: AuthService,
    private alertService: AlertService,
  ) {}

  ngOnInit(): void {
    this.loadFamilies();
  }

  loadFamilies(): void {
    const restaurantId = this.authService.getUser()?.restaurant_id;

    this.familyService.getFamilies().subscribe({
      next: (response: any) => {
        const families = Array.isArray(response)
          ? response
          : Array.isArray(response.data)
            ? response.data
            : (response.family ?? response.families ?? []);

        this.families = restaurantId
          ? families.filter(
              (family: Family) =>
                String(family.restaurant_id) === String(restaurantId),
            )
          : families;

        this.applyFamilyFilter();
      },
      error: () => {
        this.familyErrorMessages = ['No se pudieron cargar las familias.'];
      },
    });
  }

  createFamily(): void {
    const restaurantId = this.authService.getUser()?.restaurant_id;

    this.familyErrorMessages = [];

    const errors = this.validateCreateFamilyForm(restaurantId);

    if (errors.length > 0) {
      this.familyErrorMessages = errors;
      return;
    }

    const payload = {
      name: this.createFamilyForm.name.trim(),
      active: this.createFamilyForm.active,
      restaurant_id: restaurantId,
    };

    this.familyService.createFamily(payload).subscribe({
      next: () => {
        this.resetCreateFamilyForm();
        this.loadFamilies();
        this.alertService.showSuccess('Familia creada correctamente');
      },
      error: (error) => {
        this.familyErrorMessages = extractBackendErrors(error);
      },
    });
  }

  startEditFamily(family: Family): void {
    this.familyMode = 'edit';
    this.editingFamily = family;
    this.familyErrorMessages = [];

    this.editFamilyForm = {
      name: family.name ?? '',
      active: !!family.active,
    };
  }

  cancelEditFamily(): void {
    this.familyMode = 'create';
    this.editingFamily = null;
    this.familyErrorMessages = [];
    this.resetEditFamilyForm();
  }

  saveEditFamily(): void {
    if (!this.editingFamily) {
      return;
    }

    this.familyErrorMessages = [];

    const errors = this.validateEditFamilyForm();

    if (errors.length > 0) {
      this.familyErrorMessages = errors;
      return;
    }

    const payload = {
      name: this.editFamilyForm.name.trim(),
      active: this.editFamilyForm.active,
    };

    const idToUpdate = String(this.editingFamily.uuid ?? this.editingFamily.id);

    this.familyService.updateFamily(idToUpdate, payload).subscribe({
      next: () => {
        this.cancelEditFamily();
        this.loadFamilies();
        this.alertService.showSuccess('Familia actualizada correctamente');
      },
      error: (error) => {
        this.familyErrorMessages = extractBackendErrors(error);
      },
    });
  }

  async deleteFamily(family: Family): Promise<void> {
    await this.alertService.confirmDelete(
      'Eliminar familia',
      `¿Seguro que quieres eliminar "${family.name}"?`,
      () => this.confirmDeleteFamily(family),
    );
  }

  confirmDeleteFamily(family: Family): void {
    const idToDelete = String(family.uuid ?? family.id);

    this.familyService.deleteFamily(idToDelete).subscribe({
      next: () => {
        this.loadFamilies();
      },
      error: () => {
        this.familyErrorMessages = ['No se pudo eliminar la familia.'];
      },
    });
  }

  onFamilySearchChange(): void {
    this.applyFamilyFilter();
  }

  applyFamilyFilter(): void {
    const term = this.familySearchTerm.trim().toLowerCase();

    if (!term) {
      this.filteredFamilies = [...this.families];
      return;
    }

    this.filteredFamilies = this.families.filter((family) => {
      const name = family.name?.toLowerCase() ?? '';
      return name.includes(term);
    });
  }

  clearFamilySearch(): void {
    this.familySearchTerm = '';
    this.applyFamilyFilter();
  }

  resetCreateFamilyForm(): void {
    this.createFamilyForm = {
      name: '',
      active: true,
    };
    this.familyErrorMessages = [];
  }

  resetEditFamilyForm(): void {
    this.editFamilyForm = {
      name: '',
      active: true,
    };
  }

  private validateCreateFamilyForm(
    restaurantId: string | number | undefined,
  ): string[] {
    const errors: string[] = [];

    if (!restaurantId) {
      errors.push('No se ha encontrado el restaurant_id.');
    }

    if (!this.createFamilyForm.name.trim()) {
      errors.push('El nombre de la familia es obligatorio.');
    }

    return errors;
  }

  private validateEditFamilyForm(): string[] {
    const errors: string[] = [];

    if (!this.editFamilyForm.name.trim()) {
      errors.push('El nombre de la familia es obligatorio.');
    }

    return errors;
  }
}
