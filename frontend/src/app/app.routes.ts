import { Routes } from '@angular/router';
import { authGuard } from './core/auth/guards/auth.guard';
import { adminGuard } from './core/auth/guards/admin.guard';

export const routes: Routes = [
  {
    path: 'login',
    loadComponent: () =>
      import('./pages/login/login.component').then((m) => m.LoginComponent),
  },
  {
    path: 'tpv',
    canActivate: [authGuard],
    loadComponent: () =>
      import('./pages/tpv/layout/layout.component').then(
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
          import('./pages/orders/orders.component').then(
            (m) => m.OrdersComponent,
          ),
      },
      {
        path: 'settings',
        canActivate: [adminGuard],
        loadComponent: () =>
          import('./pages/settings/settings.component').then(
            (m) => m.SettingsComponent,
          ),
      },
      {
        path: 'open-orders',
        loadComponent: () =>
          import('./pages/open-orders/open-orders.component').then(
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
