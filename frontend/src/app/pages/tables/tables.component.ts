import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';
import {
  IonContent,
  IonCard,
  IonCardContent,
} from '@ionic/angular/standalone';

@Component({
  selector: 'app-tables',
  standalone: true,
  templateUrl: './tables.component.html',
  styleUrls: ['./tables.component.scss'],
  imports: [CommonModule, IonContent, IonCard, IonCardContent],
})
export class TablesComponent {
  constructor(private router: Router) {}

  tables = [
    { id: 1, name: 'Mesa 1', status: 'free' },
    { id: 2, name: 'Mesa 2', status: 'occupied' },
    { id: 3, name: 'Mesa 3', status: 'free' },
  ];

  openTable(tableId: number) {
    this.router.navigate(['/tpv/orders', tableId]);
  }
}