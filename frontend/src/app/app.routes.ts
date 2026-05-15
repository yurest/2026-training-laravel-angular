import { Routes } from '@angular/router';
import { authGuard } from './core/auth/guards/auth.guard';
import { adminGuard } from './core/auth/guards/admin.guard';

export const routes: Routes = [
  {
    path: 'login',
    loadComponent: () =>
      import('./features/identity/ui/login/login/login.component').then(
        (m) => m.LoginComponent,
      ),
  },
  {
    path: 'tpv',
    canActivate: [authGuard],
    loadComponent: () =>
      import('./core/layout/tpv-layout/layout.component').then(
        (m) => m.LayoutComponent,
      ),
    children: [
      {
        path: 'tables',
        loadComponent: () =>
          import('./features/floor/ui/tables-page/tables/tables.component').then(
            (m) => m.TablesComponent,
          ),
      },
      {
        path: 'orders/:id',
        loadComponent: () =>
          import('./features/orders/ui/order-page/orders/orders.component').then(
            (m) => m.OrdersComponent,
          ),
      },
      {
        path: 'settings',
        canActivate: [adminGuard],
        loadComponent: () =>
          import('./features/settings/ui/settings-page/settings.component').then(
            (m) => m.SettingsComponent,
          ),
      },
      {
        path: 'open-orders',
        loadComponent: () =>
          import('./features/orders/ui/open-orders-page/open-orders/open-orders.component').then(
            (m) => m.OpenOrdersComponent,
          ),
      },
      {
        path: '',
        redirectTo: 'tables',
        pathMatch: 'full',
      },
    ],
  },
  {
    path: '',
    redirectTo: 'login',
    pathMatch: 'full',
  },
  {
    path: '**',
    redirectTo: 'login',
  },
];
