import { inject } from '@angular/core';
import { CanActivateFn, Router } from '@angular/router';
import { AuthService } from '../../../services/auth/auth.service';

export const adminGuard: CanActivateFn = () => {
  const router = inject(Router);
  const authService = inject(AuthService);

  const user = authService.getUser();

  if (user?.role?.toLowerCase?.() === 'admin') {
    return true;
  }

  return router.createUrlTree(['/tpv']);
};
