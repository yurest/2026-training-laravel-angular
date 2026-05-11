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
import { AlertController } from '@ionic/angular';
import { Family, FamilyService } from '../../../../services/api/family.service';
import { AuthService } from '../../../../services/auth/auth.service';

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
    private alertController: AlertController,
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
        this.showSuccess('Familia creada correctamente');
      },
      error: (error) => {
        this.familyErrorMessages = this.extractBackendErrors(error);
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
        this.showSuccess('Familia actualizada correctamente');
      },
      error: (error) => {
        this.familyErrorMessages = this.extractBackendErrors(error);
      },
    });
  }

  async deleteFamily(family: Family): Promise<void> {
    const alert = await this.alertController.create({
      header: 'Eliminar familia',
      message: `¿Seguro que quieres eliminar "${family.name}"?`,
      cssClass: 'custom-dark-alert',
      mode: 'md',
      buttons: [
        {
          text: 'Cancelar',
          role: 'cancel',
        },
        {
          text: 'Eliminar',
          role: 'destructive',
          handler: () => {
            this.confirmDeleteFamily(family);
          },
        },
      ],
    });

    await alert.present();
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

  private extractBackendErrors(error: any): string[] {
    const errors: string[] = [];

    if (error?.error?.errors) {
      Object.values(error.error.errors).forEach((messages) => {
        if (Array.isArray(messages)) {
          messages.forEach((message) => errors.push(String(message)));
        }
      });
    }

    if (errors.length > 0) {
      return errors;
    }

    if (error?.error?.message) {
      return [String(error.error.message)];
    }

    return ['Ha ocurrido un error inesperado.'];
  }

  async showSuccess(message: string): Promise<void> {
    const alert = await this.alertController.create({
      header: 'OK',
      message,
      cssClass: 'custom-dark-alert',
      mode: 'md',
      buttons: ['Aceptar'],
    });

    await alert.present();
  }
}