import { Routes } from '@angular/router';
import { authGuard } from './guards/auth.guard';

export const routes: Routes = [
  // LOGIN (público)
  {
    path: 'login',
    loadComponent: () =>
      import('./pages/login/login.component').then((m) => m.LoginComponent),
  },

  // TPV (zona privada con layout)
  {
    path: 'tpv',
    canActivate: [authGuard],
    loadComponent: () =>
      import('./pages/tpv/layout/layout.component').then((m) => m.LayoutComponent),
    children: [
      // MESAS
      {
        path: 'tables',
        loadComponent: () =>
          import('./pages/tables/tables.component').then((m) => m.TablesComponent),
      },

      // PEDIDO POR MESA
      {
        path: 'orders/:id',
        loadComponent: () =>
          import('./pages/orders/orders.component').then((m) => m.OrdersComponent),
      },

      // REDIRECCIÓN POR DEFECTO
      {
        path: '',
        redirectTo: 'tables',
        pathMatch: 'full',
      },
    ],
  },

  // ROOT
  {
    path: '',
    redirectTo: 'login',
    pathMatch: 'full',
  },

  // CUALQUIER OTRA RUTA
  {
    path: '**',
    redirectTo: 'login',
  },
];