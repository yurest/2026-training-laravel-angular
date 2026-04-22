import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';
import { IonContent, IonCard, IonCardContent, IonButton } from '@ionic/angular/standalone';
import { TableService, TableItem } from '../../services/api/table.service';
import { ZoneService, Zone } from '../../services/api/zone.service';

@Component({
  selector: 'app-tables',
  standalone: true,
  templateUrl: './tables.component.html',
  styleUrls: ['./tables.component.scss'],
  imports: [CommonModule, IonContent, IonCard, IonCardContent, IonButton],
})
export class TablesComponent implements OnInit {
  zones: Zone[] = [];
  tables: TableItem[] = [];
  selectedZoneIndex: number | null = null;

  constructor(
    private router: Router,
    private zoneService: ZoneService,
    private tableService: TableService,
  ) {}

  ngOnInit(): void {
    this.loadZones();
    this.loadTables();
  }

  loadZones(): void {
    this.zoneService.getZones().subscribe({
      next: (response: any) => {
        const zones = Array.isArray(response)
          ? response
          : Array.isArray(response.data)
            ? response.data
            : response.zones ?? [];

        this.zones = zones;
        this.selectedZoneIndex = null;
      },
      error: (error) => {
        console.log('ERROR loading zones', error);
      },
    });
  }

  loadTables(): void {
    this.tableService.getTables().subscribe({
      next: (response: any) => {
        const tables = Array.isArray(response)
          ? response
          : Array.isArray(response.data)
            ? response.data
            : response.tables ?? [];

        this.tables = tables.map((table: TableItem, index: number) => ({
          ...table,
          status: index % 2 === 0 ? 'free' : 'occupied',
        }));
      },
      error: (error) => {
        console.log('ERROR loading tables', error);
      },
    });
  }

  selectAllZones(): void {
    this.selectedZoneIndex = null;
  }

  selectZone(index: number): void {
    this.selectedZoneIndex = index;
  }

  getFilteredTables(): TableItem[] {
    if (this.selectedZoneIndex === null) {
      return this.tables;
    }

    return this.tables.filter(
      (table: TableItem) => Number(table.zone_id) === Number(this.selectedZoneIndex)
    );
  }

  openTable(tableId: string | number): void {
    this.router.navigate(['/tpv/orders', tableId]);
  }

  isAllZonesActive(): boolean {
    return this.selectedZoneIndex === null;
  }

  isZoneActive(index: number): boolean {
    return this.selectedZoneIndex === index;
  }
}