import { inject, Injectable, Signal, signal } from '@angular/core';
import { firstValueFrom } from 'rxjs';
import { AuthService } from '../../../core/services/auth.service';
import { DeviceStorageService, LinkedRestaurant } from '../../../core/services/device-storage.service';
import { UserRole } from '../../../core/enums/user-role.enum';

export interface QuickUser {
  name: string;
  initials: string;
  userUuid: string;
  role: string;
  color: string;
}

@Injectable()
export class LoginFacade {
  private readonly authService = inject(AuthService);
  private readonly deviceStorageService = inject(DeviceStorageService);

  private readonly _employees = signal<QuickUser[]>([]);
  private readonly _linkedRestaurant = signal<LinkedRestaurant | null>(null);
  private readonly _isLoading = signal<boolean>(true);
  private readonly _isSubmitting = signal<boolean>(false);
  private readonly _errorMessage = signal<string | null>(null);

  public readonly employees: Signal<QuickUser[]> = this._employees.asReadonly();
  public readonly linkedRestaurant: Signal<LinkedRestaurant | null> = this._linkedRestaurant.asReadonly();
  public readonly isLoading: Signal<boolean> = this._isLoading.asReadonly();
  public readonly isSubmitting: Signal<boolean> = this._isSubmitting.asReadonly();
  public readonly errorMessage: Signal<string | null> = this._errorMessage.asReadonly();

  public refreshLinkedRestaurant(): void {
    this._linkedRestaurant.set(this.deviceStorageService.getLinkedRestaurant());
  }

  public clearError(): void {
    this._errorMessage.set(null);
  }

  public async loadEmployees(): Promise<void> {
    this._isLoading.set(true);
    this._errorMessage.set(null);

    try {
      const users = await firstValueFrom(
        this.authService.getQuickUsers(this.authService.getDeviceId(), this._linkedRestaurant()?.uuid),
      );

      this._employees.set(
        users.map((user) => ({
          name: user.name,
          initials: this.buildInitials(user.name),
          userUuid: user.user_uuid,
          role: user.role,
          color: this.roleColor(user.role),
        })),
      );
    } catch (error) {
      this._errorMessage.set(error instanceof Error ? error.message : 'No se pudieron cargar los empleados.');
    } finally {
      this._isLoading.set(false);
    }
  }

  public skipEmployeesLoading(): void {
    this._isLoading.set(false);
  }

  public async loginWithEmail(email: string, password: string): Promise<boolean> {
    this._isSubmitting.set(true);
    this._errorMessage.set(null);

    try {
      await firstValueFrom(this.authService.login(email, password));

      return true;
    } catch (error) {
      this._errorMessage.set(error instanceof Error ? error.message : 'No se pudo iniciar sesión.');

      return false;
    } finally {
      this._isSubmitting.set(false);
    }
  }

  public async loginWithPin(userUuid: string, pin: string): Promise<boolean> {
    this._isSubmitting.set(true);
    this._errorMessage.set(null);

    try {
      await firstValueFrom(this.authService.loginWithPin(userUuid, pin, this.authService.getDeviceId()));

      return true;
    } catch (error) {
      this._errorMessage.set(error instanceof Error ? error.message : 'No se pudo iniciar sesión con PIN.');

      return false;
    } finally {
      this._isSubmitting.set(false);
    }
  }

  private buildInitials(name: string): string {
    const parts = name.trim().split(/\s+/).filter(Boolean);
    const first = parts[0]?.charAt(0) ?? 'U';
    const second = parts[1]?.charAt(0) ?? parts[0]?.charAt(1) ?? 'S';

    return `${first}${second}`.toUpperCase();
  }

  private roleColor(role: string): string {
    if (role === UserRole.ADMIN) {
      return '#E8440A';
    }

    if (role === UserRole.SUPERVISOR) {
      return '#1A6FE8';
    }

    return '#1A9E5A';
  }
}
