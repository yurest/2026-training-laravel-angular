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
import { Zone, ZoneService } from '../../../infrastructure/zone.service';
import { AuthService } from '../../../../identity/infrastructure/auth.service';
import { AlertService } from '../../../../../shared/services/alert.service';
import { extractBackendErrors } from '../../../../../shared/helpers/extract-backend-errors.helper';

@Component({
  selector: 'app-zones-settings',
  standalone: true,
  templateUrl: './zones-settings.component.html',
  styleUrls: ['./zones-settings.component.scss'],
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
export class ZonesSettingsComponent implements OnInit {
  zones: Zone[] = [];
  filteredZones: Zone[] = [];
  zoneErrorMessages: string[] = [];
  zoneSearchTerm = '';

  zoneMode: 'create' | 'edit' = 'create';
  editingZone: Zone | null = null;

  createZoneForm = {
    name: '',
  };

  editZoneForm = {
    name: '',
  };

  constructor(
    private zoneService: ZoneService,
    private authService: AuthService,
    private alertService: AlertService,
  ) {}

  ngOnInit(): void {
    this.loadZones();
  }

  loadZones(): void {
    const restaurantId = this.authService.getUser()?.restaurant_id;

    this.zoneService.getZones().subscribe({
      next: (response: any) => {
        const zones = Array.isArray(response)
          ? response
          : Array.isArray(response.data)
            ? response.data
            : (response.zone ?? response.zones ?? []);

        this.zones = restaurantId
          ? zones.filter(
              (zone: Zone) =>
                String(zone.restaurant_id) === String(restaurantId),
            )
          : zones;

        this.applyZoneFilter();
      },
      error: () => {
        this.zoneErrorMessages = ['No se pudieron cargar las zonas.'];
      },
    });
  }

  createZone(): void {
    const restaurantId = this.authService.getUser()?.restaurant_id;

    this.zoneErrorMessages = [];

    const errors = this.validateCreateZoneForm(restaurantId);

    if (errors.length > 0) {
      this.zoneErrorMessages = errors;
      return;
    }

    const payload = {
      name: this.createZoneForm.name.trim(),
      restaurant_id: restaurantId,
    };

    this.zoneService.createZone(payload).subscribe({
      next: () => {
        this.resetCreateZoneForm();
        this.loadZones();
        this.alertService.showSuccess('Zona creada correctamente');
      },
      error: (error) => {
        this.zoneErrorMessages = extractBackendErrors(error);
      },
    });
  }

  startEditZone(zone: Zone): void {
    this.zoneMode = 'edit';
    this.editingZone = zone;
    this.zoneErrorMessages = [];

    this.editZoneForm = {
      name: zone.name ?? '',
    };
  }

  cancelEditZone(): void {
    this.zoneMode = 'create';
    this.editingZone = null;
    this.zoneErrorMessages = [];
    this.resetEditZoneForm();
  }

  saveEditZone(): void {
    if (!this.editingZone) {
      return;
    }

    this.zoneErrorMessages = [];

    const errors = this.validateEditZoneForm();

    if (errors.length > 0) {
      this.zoneErrorMessages = errors;
      return;
    }

    const payload = {
      name: this.editZoneForm.name.trim(),
    };

    const idToUpdate = String(this.editingZone.uuid ?? this.editingZone.id);

    this.zoneService.updateZone(idToUpdate, payload).subscribe({
      next: () => {
        this.cancelEditZone();
        this.loadZones();
        this.alertService.showSuccess('Zona actualizada correctamente');
      },
      error: (error) => {
        this.zoneErrorMessages = extractBackendErrors(error);
      },
    });
  }

  async deleteZone(zone: Zone): Promise<void> {
    await this.alertService.confirmDelete(
      'Eliminar zona',
      `¿Seguro que quieres eliminar "${zone.name}"?`,
      () => this.confirmDeleteZone(zone),
    );
  }

  confirmDeleteZone(zone: Zone): void {
    const idToDelete = String(zone.uuid ?? zone.id);

    this.zoneService.deleteZone(idToDelete).subscribe({
      next: () => {
        this.loadZones();
      },
      error: () => {
        this.zoneErrorMessages = ['No se pudo eliminar la zona.'];
      },
    });
  }

  onZoneSearchChange(): void {
    this.applyZoneFilter();
  }

  applyZoneFilter(): void {
    const term = this.zoneSearchTerm.trim().toLowerCase();

    if (!term) {
      this.filteredZones = [...this.zones];
      return;
    }

    this.filteredZones = this.zones.filter((zone) => {
      const name = zone.name?.toLowerCase() ?? '';
      return name.includes(term);
    });
  }

  clearZoneSearch(): void {
    this.zoneSearchTerm = '';
    this.applyZoneFilter();
  }

  resetCreateZoneForm(): void {
    this.createZoneForm = {
      name: '',
    };
    this.zoneErrorMessages = [];
  }

  resetEditZoneForm(): void {
    this.editZoneForm = {
      name: '',
    };
  }

  private validateCreateZoneForm(
    restaurantId: string | number | undefined,
  ): string[] {
    const errors: string[] = [];

    if (!restaurantId) {
      errors.push('No se ha encontrado el restaurant_id.');
    }

    if (!this.createZoneForm.name.trim()) {
      errors.push('El nombre de la zona es obligatorio.');
    }

    return errors;
  }

  private validateEditZoneForm(): string[] {
    const errors: string[] = [];

    if (!this.editZoneForm.name.trim()) {
      errors.push('El nombre de la zona es obligatorio.');
    }

    return errors;
  }
}