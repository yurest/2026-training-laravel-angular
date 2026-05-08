
import { Component, computed, input } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { GestionZonesFacade, TableRow, ZoneRow, ZoneFormData, TableFormData } from '../../../pages/core/gestion/facades/gestion-zones.facade';

@Component({
  selector: 'app-zones-management',
  standalone: true,
  imports: [FormsModule],
  templateUrl: './zones-management.component.html',
  styleUrls: ['./zones-management.component.scss'],
})
export class ZonesManagementComponent {
  public readonly facade = input.required<GestionZonesFacade>();

  public readonly zones = computed(() => this.facade().zones());
  public readonly selectedZone = computed(() => this.facade().selectedZone());
  public readonly selectedZoneIndex = computed(() => this.facade().selectedZoneIndex());
  public readonly selectedTableIndex = computed(() => this.facade().selectedTableIndex());
  public readonly zoneFormData = computed(() => this.facade().zoneFormData());
  public readonly tableFormData = computed(() => this.facade().tableFormData());
  public readonly isSavingZone = computed(() => this.facade().isSavingZone());
  public readonly isSavingTable = computed(() => this.facade().isSavingTable());

  isZoneSelected(index: number): boolean {
    return this.selectedZoneIndex() === index;
  }

  isTableSelected(index: number): boolean {
    return this.selectedTableIndex() === index;
  }

  onSelectZone(index: number): void {
    this.facade().selectZone(index);
  }

  onCreateZone(): void {
    this.facade().startCreateZone();
  }

  async onDeleteZone(): Promise<void> {
    const result = await this.facade().deleteSelectedZone();
    if (result.ok) {
      window.alert(result.message || 'Zona eliminada.');
    } else {
      window.alert(result.error || 'No se pudo eliminar la zona.');
    }
  }

  async onSubmitZone(): Promise<void> {
    const result = await this.facade().saveZone();
    if (result.ok) {
      window.alert(result.message || 'Zona guardada.');
    } else {
      window.alert(result.error || 'No se pudo guardar la zona.');
    }
  }

  onSelectTable(index: number): void {
    this.facade().selectTable(index);
  }

  onCreateTable(): void {
    this.facade().startCreateTable();
  }

  async onDeleteTable(): Promise<void> {
    const result = await this.facade().deleteSelectedTable();
    if (result.ok) {
      window.alert(result.message || 'Mesa eliminada.');
    } else {
      window.alert(result.error || 'No se pudo eliminar la mesa.');
    }
  }

  async onSubmitTable(): Promise<void> {
    const result = await this.facade().saveTable();
    if (result.ok) {
      window.alert(result.message || 'Mesa guardada.');
    } else {
      window.alert(result.error || 'No se pudo guardar la mesa.');
    }
  }

  updateZoneForm<K extends keyof ZoneFormData>(key: K, value: ZoneFormData[K]): void {
    this.facade().updateZoneForm(key, value);
  }

  updateTableForm<K extends keyof TableFormData>(key: K, value: TableFormData[K]): void {
    this.facade().updateTableForm(key, value);
  }
}
