import { Routes } from '@angular/router';
import { authGuard } from './guards/auth.guard';

export const routes: Routes = [
  {
  path: 'login',
  loadComponent: () =>
    import('./pages/login/login.component').then((m) => m.LoginComponent),
},
  {
    path: 'home',
    canActivate: [authGuard],
    loadComponent: () =>
      import('./pages/core/home/home.page').then((m) => m.HomePage),
  },
  {
    path: '',
    redirectTo: 'login',
    pathMatch: 'full',
  },
];