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
  IonSelect,
  IonSelectOption,
} from '@ionic/angular/standalone';
import { TableItem, TableService } from '../../../../services/api/table.service';
import { Zone, ZoneService } from '../../../../services/api/zone.service';
import { AuthService } from '../../../../services/auth/auth.service';
import { AlertService } from '../../../../shared/services/alert.service';
import { extractBackendErrors } from '../../../../shared/helpers/extract-backend-errors.helper';

@Component({
  selector: 'app-tables-settings',
  standalone: true,
  templateUrl: './tables-settings.component.html',
  styleUrls: ['./tables-settings.component.scss'],
  imports: [
    CommonModule,
    FormsModule,
    IonCard,
    IonCardContent,
    IonButton,
    IonItem,
    IonLabel,
    IonInput,
    IonSelect,
    IonSelectOption,
  ],
})
export class TablesSettingsComponent implements OnInit {
  zones: Zone[] = [];

  tables: TableItem[] = [];
  filteredTables: TableItem[] = [];
  tableErrorMessages: string[] = [];
  tableSearchTerm = '';

  tableMode: 'create' | 'edit' = 'create';
  editingTable: TableItem | null = null;

  createTableForm = {
    name: '',
    zone_id: '',
  };

  editTableForm = {
    name: '',
    zone_id: '',
  };

  constructor(
    private zoneService: ZoneService,
    private tableService: TableService,
    private authService: AuthService,
    private alertService: AlertService,
  ) {}

  ngOnInit(): void {
    this.loadZones();
    this.loadTables();
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
      },
      error: () => {
        this.tableErrorMessages = ['No se pudieron cargar las zonas.'];
      },
    });
  }

  loadTables(): void {
    const restaurantId = this.authService.getUser()?.restaurant_id;

    this.tableService.getTables().subscribe({
      next: (response: any) => {
        const tables = Array.isArray(response)
          ? response
          : Array.isArray(response.data)
            ? response.data
            : (response.table ?? response.tables ?? []);

        this.tables = restaurantId
          ? tables.filter(
              (table: TableItem) =>
                String(table.restaurant_id) === String(restaurantId),
            )
          : tables;

        this.applyTableFilter();
      },
      error: () => {
        this.tableErrorMessages = ['No se pudieron cargar las mesas.'];
      },
    });
  }

  createTable(): void {
    const restaurantId = this.authService.getUser()?.restaurant_id;

    this.tableErrorMessages = [];

    const errors = this.validateCreateTableForm(restaurantId);

    if (errors.length > 0) {
      this.tableErrorMessages = errors;
      return;
    }

    const payload = {
      name: this.createTableForm.name.trim(),
      zone_id: this.createTableForm.zone_id,
      restaurant_id: restaurantId,
    };

    this.tableService.createTable(payload).subscribe({
      next: () => {
        this.resetCreateTableForm();
        this.loadTables();
        this.alertService.showSuccess('Mesa creada correctamente');
      },
      error: (error) => {
        this.tableErrorMessages = extractBackendErrors(error);
      },
    });
  }

  startEditTable(table: TableItem): void {
    this.tableMode = 'edit';
    this.editingTable = table;
    this.tableErrorMessages = [];

    this.editTableForm = {
      name: table.name ?? '',
      zone_id: String(table.zone_id ?? ''),
    };
  }

  cancelEditTable(): void {
    this.tableMode = 'create';
    this.editingTable = null;
    this.tableErrorMessages = [];
    this.resetEditTableForm();
  }

  saveEditTable(): void {
    if (!this.editingTable) {
      return;
    }

    this.tableErrorMessages = [];

    const errors = this.validateEditTableForm();

    if (errors.length > 0) {
      this.tableErrorMessages = errors;
      return;
    }

    const payload = {
      name: this.editTableForm.name.trim(),
      zone_id: this.editTableForm.zone_id,
    };

    const idToUpdate = String(this.editingTable.uuid ?? this.editingTable.id);

    this.tableService.updateTable(idToUpdate, payload).subscribe({
      next: () => {
        this.cancelEditTable();
        this.loadTables();
        this.alertService.showSuccess('Mesa actualizada correctamente');
      },
      error: (error) => {
        this.tableErrorMessages = extractBackendErrors(error);
      },
    });
  }

  async deleteTable(table: TableItem): Promise<void> {
    await this.alertService.confirmDelete(
      'Eliminar mesa',
      `¿Seguro que quieres eliminar "${table.name}"?`,
      () => this.confirmDeleteTable(table),
    );
  }

  confirmDeleteTable(table: TableItem): void {
    const idToDelete = String(table.uuid ?? table.id);

    this.tableService.deleteTable(idToDelete).subscribe({
      next: () => {
        this.loadTables();
      },
      error: () => {
        this.tableErrorMessages = ['No se pudo eliminar la mesa.'];
      },
    });
  }

  onTableSearchChange(): void {
    this.applyTableFilter();
  }

  applyTableFilter(): void {
    const term = this.tableSearchTerm.trim().toLowerCase();

    if (!term) {
      this.filteredTables = [...this.tables];
      return;
    }

    this.filteredTables = this.tables.filter((table) => {
      const name = table.name?.toLowerCase() ?? '';
      return name.includes(term);
    });
  }

  clearTableSearch(): void {
    this.tableSearchTerm = '';
    this.applyTableFilter();
  }

  resetCreateTableForm(): void {
    this.createTableForm = {
      name: '',
      zone_id: '',
    };
    this.tableErrorMessages = [];
  }

  resetEditTableForm(): void {
    this.editTableForm = {
      name: '',
      zone_id: '',
    };
  }

  getZoneName(zoneId: string | number): string {
    const zone = this.zones.find(
      (item) =>
        String(item.id) === String(zoneId) ||
        String(item.uuid) === String(zoneId),
    );

    return zone?.name ?? `Zona ${zoneId}`;
  }

  private validateCreateTableForm(
    restaurantId: string | number | undefined,
  ): string[] {
    const errors: string[] = [];

    if (!restaurantId) {
      errors.push('No se ha encontrado el restaurant_id.');
    }

    if (!this.createTableForm.name.trim()) {
      errors.push('El nombre de la mesa es obligatorio.');
    }

    if (!this.createTableForm.zone_id) {
      errors.push('Debes seleccionar una zona.');
    }

    return errors;
  }

  private validateEditTableForm(): string[] {
    const errors: string[] = [];

    if (!this.editTableForm.name.trim()) {
      errors.push('El nombre de la mesa es obligatorio.');
    }

    if (!this.editTableForm.zone_id) {
      errors.push('Debes seleccionar una zona.');
    }

    return errors;
  }
}