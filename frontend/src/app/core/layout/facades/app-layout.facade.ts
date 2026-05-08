import { Injectable, signal, computed, inject } from '@angular/core';
import { Subject, interval, take, takeUntil } from 'rxjs';
import { filter } from 'rxjs/operators';
import { NavigationEnd, Router } from '@angular/router';
import { AppContextService } from '../../services/app-context.service';
import { AuthService, AuthUser } from '../../services/auth.service';
import { TpvService } from '../../../features/cash/services/tpv.service';

@Injectable({
  providedIn: 'root'
})
export class AppLayoutFacade {
  private readonly authService = inject(AuthService);
  private readonly contextService = inject(AppContextService);
  private readonly tpvService = inject(TpvService);
  private readonly router = inject(Router);

  private readonly destroy$ = new Subject<void>();

  // Signals for layout state
  private readonly currentDateTime = signal<Date>(new Date());
  private readonly currentUser = signal<AuthUser | null>(null);
  private readonly activeRestaurantName = signal<string>('Sin restaurante');
  private readonly isAdminUser = signal<boolean>(false);
  private readonly isPedidosOrComanda = signal<boolean>(false);
  private readonly isCajaOpen = signal<boolean>(false);
  private readonly cajaLoaded = signal<boolean>(false);

  // Readonly signals for external consumption
  public readonly dateTime = computed(() => this.currentDateTime());
  public readonly user = computed(() => this.currentUser());
  public readonly restaurantName = computed(() => this.activeRestaurantName());
  public readonly adminUser = computed(() => this.isAdminUser());
  public readonly pedidosOrComanda = computed(() => this.isPedidosOrComanda());
  public readonly cajaOpen = computed(() => this.isCajaOpen());
  public readonly cajaStatusLoaded = computed(() => this.cajaLoaded());

  // Getters for compatibility
  public get currentUserValue(): AuthUser | null {
    return this.currentUser();
  }

  public get currentDateTimeValue(): Date {
    return this.currentDateTime();
  }

  public get activeRestaurantNameValue(): string {
    return this.activeRestaurantName();
  }

  public get isAdminUserValue(): boolean {
    return this.isAdminUser();
  }

  public get isPedidosOrComandaValue(): boolean {
    return this.isPedidosOrComanda();
  }

  public get isCajaOpenValue(): boolean {
    return this.isCajaOpen();
  }

  public get cajaLoadedValue(): boolean {
    return this.cajaLoaded();
  }

  // State setters
  public setCurrentDateTime(value: Date): void {
    this.currentDateTime.set(value);
  }

  public setCurrentUser(value: AuthUser | null): void {
    this.currentUser.set(value);
  }

  public setActiveRestaurantName(value: string): void {
    this.activeRestaurantName.set(value);
  }

  public setIsAdminUser(value: boolean): void {
    this.isAdminUser.set(value);
  }

  public setIsPedidosOrComanda(value: boolean): void {
    this.isPedidosOrComanda.set(value);
  }

  public setIsCajaOpen(value: boolean): void {
    this.isCajaOpen.set(value);
  }

  public setCajaLoaded(value: boolean): void {
    this.cajaLoaded.set(value);
  }

  // Helper methods
  public getDeviceId(): string {
    return this.authService.getDeviceId();
  }

  public refreshCajaStatus(): void {
    const deviceId = this.getDeviceId();
    if (!deviceId) return;
    this.tpvService.getActiveCashSession(deviceId).pipe(take(1)).subscribe({
      next: (session) => {
        this.setIsCajaOpen(session?.status === 'open');
        this.setCajaLoaded(true);
      },
      error: () => {
        this.setIsCajaOpen(false);
        this.setCajaLoaded(true);
      },
    });
  }

  public startTimer(): void {
    interval(1000).pipe(takeUntil(this.destroy$)).subscribe(() => {
      this.setCurrentDateTime(new Date());
    });
  }

  public startRouterListener(): void {
    this.router.events
      .pipe(filter((event) => event instanceof NavigationEnd), takeUntil(this.destroy$))
      .subscribe(() => {
        this.setIsPedidosOrComanda(
          this.router.url.startsWith('/app/pedidos') || this.router.url.startsWith('/app/comanda')
        );
        this.refreshCajaStatus();
      });
  }

  public restoreSession(): void {
    this.authService.restoreSession().pipe(take(1)).subscribe({
      next: () => undefined,
      error: () => {
        this.setCurrentUser(null);
        this.setIsAdminUser(false);
      },
    });
  }

  public logout(): void {
    this.authService.logout().pipe(take(1)).subscribe({
      next: () => {
        this.contextService.clearActiveRestaurant();
        this.setIsAdminUser(false);
        this.router.navigateByUrl('/login');
      },
      error: () => {
        this.contextService.clearActiveRestaurant();
        this.setIsAdminUser(false);
        this.router.navigateByUrl('/login');
      },
    });
  }

  public goToCaja(): void {
    this.router.navigateByUrl('/app/caja');
  }

  public getCurrentUserInitials(): string {
    const user = this.currentUser();
    if (!user?.name) {
      return 'US';
    }

    const parts = user.name.trim().split(/\s+/).filter(Boolean);
    const first = parts[0]?.charAt(0) ?? 'U';
    const second = parts[1]?.charAt(0) ?? parts[0]?.charAt(1) ?? 'S';

    return `${first}${second}`.toUpperCase();
  }

  public getTopbarDateText(): string {
    const date = this.currentDateTime();
    const datePart = new Intl.DateTimeFormat('es-ES', {
      weekday: 'long',
      day: 'numeric',
      month: 'long',
    }).format(date);

    const timePart = new Intl.DateTimeFormat('es-ES', {
      hour: '2-digit',
      minute: '2-digit',
      hour12: false,
    }).format(date);

    return `${datePart} · ${timePart}`;
  }

  public ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }
}
