import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';
import { AlertController } from '@ionic/angular';
import { IonContent, IonCard, IonCardContent } from '@ionic/angular/standalone';
import { TableItem, TableService } from '../../../infrastructure/table.service';
import { Zone, ZoneService } from '../../../infrastructure/zone.service';
import { Order, OrderService } from '../../../../../services/api/order.service';
import { AuthService } from '../../../../../services/auth/auth.service';

interface TableWithOrder extends TableItem {
  status: 'free' | 'occupied';
  activeOrderId?: string;
}

@Component({
  selector: 'app-tables',
  standalone: true,
  templateUrl: './tables.component.html',
  styleUrls: ['./tables.component.scss'],
  imports: [CommonModule, IonContent, IonCard, IonCardContent],
})
export class TablesComponent implements OnInit {
  zones: Zone[] = [];
  tables: TableWithOrder[] = [];
  orders: Order[] = [];

  selectedZoneNumericId: number | null = null;
  user: any = null;

  constructor(
    private router: Router,
    private zoneService: ZoneService,
    private tableService: TableService,
    private orderService: OrderService,
    private authService: AuthService,
    private alertController: AlertController,
  ) {}

  ngOnInit(): void {
    this.user = this.authService.getUser();
    this.loadData();
  }

  loadData(): void {
    this.loadZones();
    this.loadTablesAndOrders();
  }

  loadZones(): void {
    this.zoneService.getZones().subscribe({
      next: (response: any) => {
        this.zones = this.extractArray(response, 'zones');
      },
      error: (error: unknown) => {
        console.log('ERROR loading zones', error);
      },
    });
  }

  loadTablesAndOrders(): void {
    this.tableService.getTables().subscribe({
      next: (tablesResponse: any) => {
        const tables = this.extractArray(tablesResponse, 'tables');

        const restaurantId = this.user?.restaurant_id;

        if (!restaurantId) {
          this.tables = this.buildTablesWithStatus(tables);
          return;
        }

        this.orderService.getOpenOrders(restaurantId).subscribe({
          next: (ordersResponse: any) => {
            this.orders = this.extractArray(ordersResponse, 'orders');
            this.tables = this.buildTablesWithStatus(tables);
          },
          error: (error: unknown) => {
            console.log('ERROR loading open orders', error);
            this.tables = this.buildTablesWithStatus(tables);
          },
        });
      },
      error: (error: unknown) => {
        console.log('ERROR loading tables', error);
      },
    });
  }

  buildTablesWithStatus(tables: TableItem[]): TableWithOrder[] {
    return tables.map((table) => {
      const activeOrder = this.orders.find((order) => {
        return String(order.table_id) === String(table.id);
      });

      return {
        ...table,
        status: activeOrder ? 'occupied' : 'free',
        activeOrderId: activeOrder?.id,
      };
    });
  }

  selectZone(zoneNumericId: number | undefined): void {
    if (!zoneNumericId) {
      return;
    }

    this.selectedZoneNumericId = zoneNumericId;
  }

  selectAllZones(): void {
    this.selectedZoneNumericId = null;
  }

  isAllZonesActive(): boolean {
    return this.selectedZoneNumericId === null;
  }

  isZoneActive(zoneNumericId: number | undefined): boolean {
    return this.selectedZoneNumericId === zoneNumericId;
  }

  getTablesByZone(zoneNumericId: number | undefined): TableWithOrder[] {
    if (!zoneNumericId) {
      return [];
    }

    return this.tables.filter(
      (table) => Number(table.zone_id) === Number(zoneNumericId),
    );
  }

  getSelectedZoneName(): string {
    const zone = this.zones.find(
      (item) => item.numeric_id === this.selectedZoneNumericId,
    );

    return zone?.name ?? '';
  }

  openTable(table: TableWithOrder): void {
    if (table.status === 'occupied' && table.activeOrderId) {
      this.router.navigate(['/tpv/orders', table.activeOrderId]);
      return;
    }

    this.openCreateOrderModal(table);
  }

  async openCreateOrderModal(table: TableWithOrder): Promise<void> {
    const alert = await this.alertController.create({
      header: `Abrir pedido - ${table.name}`,
      message: 'Indica el número de comensales',
      cssClass: 'custom-dark-alert',
      mode: 'md',
      inputs: [
        {
          name: 'diners',
          type: 'number',
          min: 1,
          value: 2,
          placeholder: 'Comensales',
        },
      ],
      buttons: [
        {
          text: 'Cancelar',
          role: 'cancel',
        },
        {
          text: 'Abrir pedido',
          handler: (data) => {
            const diners = Number(data.diners);

            if (!diners || diners < 1) {
              return false;
            }

            this.createOrderFromTable(table, diners);
            return true;
          },
        },
      ],
    });

    await alert.present();
  }

  createOrderFromTable(table: TableWithOrder, diners: number): void {
    if (!this.user) {
      return;
    }

    const payload = {
      restaurant_id: Number(this.user.restaurant_id),
      table_id: table.id,
      opened_by_user_id: this.user.id,
      diners,
    };

    this.orderService.createOrder(payload).subscribe({
      next: (response: any) => {
        const orderId = response?.id ?? response?.order?.id;

        if (!orderId) {
          console.log('ORDER CREATED BUT ID NOT FOUND', response);
          this.loadTablesAndOrders();
          return;
        }

        this.router.navigate(['/tpv/orders', orderId]);
      },
      error: (error: unknown) => {
        console.log('ERROR creating order from table', error);
      },
    });
  }

  private extractArray(response: any, key: string): any[] {
    if (Array.isArray(response)) {
      return response;
    }

    if (Array.isArray(response?.data)) {
      return response.data;
    }

    if (Array.isArray(response?.[key])) {
      return response[key];
    }

    return [];
  }
}