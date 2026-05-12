import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import {
  IonContent,
  IonCard,
  IonCardContent,
  IonButton,
} from '@ionic/angular/standalone';
import { Router } from '@angular/router';
import { Order, OrderService } from '../../services/api/order.service';
import { TableItem, TableService } from '../../services/api/table.service';
import { AuthService } from '../../services/auth/auth.service';

@Component({
  selector: 'app-open-orders',
  standalone: true,
  templateUrl: './open-orders.component.html',
  styleUrls: ['./open-orders.component.scss'],
  imports: [CommonModule, IonContent, IonCard, IonCardContent, IonButton],
})
export class OpenOrdersComponent implements OnInit {
  orders: Order[] = [];
  tables: TableItem[] = [];

  loading = false;
  errorMessage = '';

  constructor(
    private router: Router,
    private orderService: OrderService,
    private tableService: TableService,
    private authService: AuthService,
  ) {}

  ngOnInit(): void {
    this.loadData();
  }

  loadData(): void {
    this.loading = true;
    this.errorMessage = '';

    this.loadTables();
    this.loadOpenOrders();
  }

  loadTables(): void {
    this.tableService.getTables().subscribe({
      next: (response: any) => {
        this.tables = this.extractArray(response, 'tables');
      },
      error: (error: any) => {
        console.log('ERROR loading tables', error);
      },
    });
  }

  loadOpenOrders(): void {
    const restaurantId = this.authService.getUser()?.restaurant_id;

    if (!restaurantId) {
      this.errorMessage = 'No se ha encontrado el restaurant_id.';
      this.loading = false;
      return;
    }

    this.orderService.getOpenOrders(restaurantId).subscribe({
      next: (response: any) => {
        this.orders = this.extractArray(response, 'orders');
        this.loading = false;
      },
      error: (error: any) => {
        console.log('ERROR loading open orders', error);
        this.errorMessage = 'No se pudieron cargar los pedidos abiertos.';
        this.loading = false;
      },
    });
  }

  openOrder(order: Order): void {
    this.router.navigate(['/tpv/orders', order.id]);
  }

  getTableName(tableId: string | number): string {
    const table = this.tables.find((item) => String(item.id) === String(tableId));

    return table?.name ?? `Mesa ${tableId}`;
  }

  formatDate(date?: string): string {
    if (!date) {
      return '-';
    }

    return new Date(date).toLocaleString('es-ES', {
      day: '2-digit',
      month: '2-digit',
      hour: '2-digit',
      minute: '2-digit',
    });
  }

  private extractArray(response: any, key: string): any[] {
    if (Array.isArray(response)) return response;
    if (Array.isArray(response?.data)) return response.data;
    if (Array.isArray(response?.[key])) return response[key];

    return [];
  }
}