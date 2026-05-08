import { computed, inject, Injectable, Signal, signal } from '@angular/core';
import { firstValueFrom } from 'rxjs';
import { RestaurantService, AdminRestaurantUser } from '../../../../services/restaurant.service';

export interface UserRow {
  uuid?: string;
  name: string;
  email: string;
  role: string;
}

export interface UserFormData {
  name: string;
  email: string;
  role: string;
  pin: string;
  password: string;
}

const EMPTY_FORM: UserFormData = {
  name: '',
  email: '',
  role: 'operator',
  pin: '',
  password: '',
};

export interface OperationResult {
  ok: boolean;
  error?: string;
  message?: string;
}

@Injectable()
export class GestionUsersFacade {
  private readonly restaurantService = inject(RestaurantService);

  private readonly _users = signal<UserRow[]>([]);
  private readonly _selectedIndex = signal<number>(-1);
  private readonly _formData = signal<UserFormData>({ ...EMPTY_FORM });
  private readonly _isSaving = signal<boolean>(false);
  private readonly _isLoading = signal<boolean>(false);

  public readonly users: Signal<UserRow[]> = this._users.asReadonly();
  public readonly selectedIndex: Signal<number> = this._selectedIndex.asReadonly();
  public readonly formData: Signal<UserFormData> = this._formData.asReadonly();
  public readonly isSaving: Signal<boolean> = this._isSaving.asReadonly();
  public readonly isLoading: Signal<boolean> = this._isLoading.asReadonly();

  public readonly selectedUser: Signal<UserRow | null> = computed(() => {
    const index = this._selectedIndex();
    const list = this._users();

    return index >= 0 && index < list.length ? list[index] : null;
  });

  public async load(restaurantUuid: string): Promise<void> {
    this._isLoading.set(true);

    try {
      const response = await firstValueFrom(this.restaurantService.getRestaurantUsers(restaurantUuid));
      const rows: UserRow[] = response.users.map((user: AdminRestaurantUser) => ({
        uuid: user.uuid,
        name: user.name,
        email: user.email,
        role: this.normalizeRole(user.role),
      }));

      this._users.set(rows);
      this.syncFormFromIndex();
    } finally {
      this._isLoading.set(false);
    }
  }

  public clear(): void {
    this._users.set([]);
    this._selectedIndex.set(-1);
    this._formData.set({ ...EMPTY_FORM });
  }

  public select(index: number): void {
    this._selectedIndex.set(index);
    this.syncFormFromIndex();
  }

  public startCreate(): void {
    this._selectedIndex.set(-1);
    this._formData.set({ ...EMPTY_FORM });
  }

  public updateForm<K extends keyof UserFormData>(key: K, value: UserFormData[K]): void {
    this._formData.update((current) => ({ ...current, [key]: value }));
  }

  public setForm(data: UserFormData): void {
    this._formData.set({ ...data });
  }

  public async deleteSelected(restaurantUuid: string): Promise<OperationResult> {
    const user = this.selectedUser();

    if (!user?.uuid) {
      return { ok: false, error: 'No se puede eliminar: usuario sin identificador.' };
    }

    try {
      await firstValueFrom(this.restaurantService.deleteRestaurantUser(restaurantUuid, user.uuid));
      const newList = this._users().filter((candidate) => candidate.uuid !== user.uuid);
      this._users.set(newList);
      this._selectedIndex.set(newList.length > 0 ? 0 : -1);
      this.syncFormFromIndex();

      return { ok: true, message: `Usuario "${user.name}" eliminado.` };
    } catch (error) {
      return { ok: false, error: error instanceof Error ? error.message : 'No se pudo eliminar el usuario.' };
    }
  }

  public async delete(restaurantUuid: string, uuid: string): Promise<OperationResult> {
    try {
      await firstValueFrom(this.restaurantService.deleteRestaurantUser(restaurantUuid, uuid));
      const newList = this._users().filter((candidate) => candidate.uuid !== uuid);
      this._users.set(newList);
      const currentIndex = this._selectedIndex();
      this._selectedIndex.set(newList.length > 0 ? Math.min(currentIndex, newList.length - 1) : -1);
      this.syncFormFromIndex();

      return { ok: true, message: 'Usuario eliminado.' };
    } catch (error) {
      return { ok: false, error: error instanceof Error ? error.message : 'No se pudo eliminar el usuario.' };
    }
  }

  public async save(restaurantUuid: string): Promise<OperationResult> {
    const form = this._formData();
    const name = form.name.trim();
    const email = form.email.trim();
    const role = form.role;
    const pin = form.pin.trim();
    const password = form.password.trim();

    if (!name || !email || !role) {
      return { ok: false, error: 'Revisa los datos del usuario.' };
    }

    const selected = this.selectedUser();

    this._isSaving.set(true);

    try {
      if (selected?.uuid) {
        const payload: any = { name, email, role };
        if (pin) payload.pin = pin;
        if (password) payload.password = password;

        await firstValueFrom(this.restaurantService.updateRestaurantUser(restaurantUuid, selected.uuid, payload));

        this.replaceUser(selected.uuid, {
          uuid: selected.uuid,
          name,
          email,
          role,
        });

        this.syncFormFromIndex();

        return { ok: true, message: 'Usuario actualizado.' };
      }

      const payload: any = { name, email, password, role };
      if (pin) payload.pin = pin;

      const created = await firstValueFrom(this.restaurantService.createRestaurantUser(restaurantUuid, payload));

      const newRow: UserRow = {
        uuid: created.uuid,
        name: created.name,
        email: created.email,
        role: this.normalizeRole(created.role),
      };

      const newList = [...this._users(), newRow];
      this._users.set(newList);
      this._selectedIndex.set(newList.length - 1);
      this.syncFormFromIndex();

      return { ok: true, message: 'Usuario creado.' };
    } catch (error) {
      return { ok: false, error: error instanceof Error ? error.message : 'No se pudo guardar el usuario.' };
    } finally {
      this._isSaving.set(false);
    }
  }

  private replaceUser(uuid: string, replacement: UserRow): void {
    this._users.update((current) => current.map((user) => (user.uuid === uuid ? replacement : user)));
  }

  private syncFormFromIndex(): void {
    const user = this.selectedUser();

    if (user) {
      this._formData.set({
        name: user.name,
        email: user.email,
        role: user.role,
        pin: '',
        password: '',
      });
    } else {
      this._formData.set({ ...EMPTY_FORM });
    }
  }

  private normalizeRole(role: string): string {
    const roleMap: Record<string, string> = {
      admin: 'admin',
      manager: 'manager',
      operator: 'operator',
    };

    return roleMap[role.toLowerCase()] || role.toLowerCase();
  }
}
