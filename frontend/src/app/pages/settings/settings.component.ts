import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import {
  IonContent,
  IonCard,
  IonCardContent,
  IonButton,
  IonItem,
  IonLabel,
  IonInput,
  IonSelect,
  IonSelectOption,
  IonCheckbox,
} from '@ionic/angular/standalone';
import { AlertController } from '@ionic/angular';
import { UserService, User } from '../../services/api/user.service';
import { AuthService } from '../../services/auth/auth.service';
import { ProductsSettingsComponent } from './components/products-settings/products-settings.component';
import { FamiliesSettingsComponent } from './components/families-settings/families-settings.component';
import { TaxesSettingsComponent } from './components/taxes-settings/taxes-settings.component';
import { ZonesSettingsComponent } from './components/zones-settings/zones-settings.component';
import { TablesSettingsComponent } from './components/tables-settings/tables-settings.component';


type SettingsSection =
  | 'users'
  | 'products'
  | 'families'
  | 'taxes'
  | 'zones'
  | 'tables';

@Component({
  selector: 'app-settings',
  standalone: true,
  templateUrl: './settings.component.html',
  styleUrls: ['./settings.component.scss'],
  imports: [
    CommonModule,
    FormsModule,
    IonContent,
    IonCard,
    IonCardContent,
    IonButton,
    IonItem,
    IonLabel,
    IonInput,
    IonSelect,
    IonSelectOption,
    IonCheckbox,
    ProductsSettingsComponent,
    FamiliesSettingsComponent,
    TaxesSettingsComponent,
    ZonesSettingsComponent,
    TablesSettingsComponent,
  ],
})
export class SettingsComponent implements OnInit {
  selectedSection: SettingsSection = 'users';

  users: User[] = [];
  filteredUsers: User[] = [];
  loading = false;
  errorMessages: string[] = [];
  searchTerm = '';

  mode: 'create' | 'edit' = 'create';
  editingUser: User | null = null;

  createForm = {
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'operator',
    pin: '',
    image_src: '',
  };

  editForm = {
    name: '',
    email: '',
    role: 'operator',
    pin: '',
    image_src: '',
    password: '',
    password_confirmation: '',
  };


  constructor(
    private userService: UserService,
    private authService: AuthService,
    private alertController: AlertController,
  ) {}

  ngOnInit(): void {
    this.loadUsers();
  }

  selectSection(section: SettingsSection): void {
    this.selectedSection = section;
    this.errorMessages = [];

    if (section === 'users') {
      this.loadUsers();
    }
  }

  isSectionActive(section: SettingsSection): boolean {
    return this.selectedSection === section;
  }

  loadUsers(): void {
    this.loading = true;
    this.errorMessages = [];

    const restaurantId = this.authService.getUser()?.restaurant_id;

    this.userService.getUsers().subscribe({
      next: (response: any) => {
        const users = Array.isArray(response)
          ? response
          : Array.isArray(response.data)
            ? response.data
            : (response.users ?? []);

        this.users = restaurantId
          ? users.filter(
              (user: User) =>
                String(user.restaurant_id) === String(restaurantId),
            )
          : users;

        this.applyUserFilter();
        this.loading = false;
      },
      error: () => {
        this.errorMessages = ['No se pudieron cargar los usuarios.'];
        this.loading = false;
      },
    });
  }

  createUser(): void {
    const restaurantId = this.authService.getUser()?.restaurant_id;

    this.errorMessages = [];

    const errors = this.validateCreateUserForm(restaurantId);

    if (errors.length > 0) {
      this.errorMessages = errors;
      return;
    }

    const payload = {
      name: this.createForm.name.trim(),
      email: this.createForm.email.trim(),
      password: this.createForm.password,
      password_confirmation: this.createForm.password_confirmation,
      role: this.createForm.role,
      pin: this.createForm.pin.trim() || null,
      image_src: this.createForm.image_src.trim() || null,
      restaurant_id: restaurantId,
    };

    this.userService.createUser(payload).subscribe({
      next: () => {
        this.resetCreateForm();
        this.loadUsers();
        this.showSuccess('Usuario creado correctamente');
      },
      error: (error) => {
        this.errorMessages = this.extractBackendErrors(error);
      },
    });
  }

  startEdit(user: User): void {
    this.mode = 'edit';
    this.editingUser = user;
    this.errorMessages = [];

    this.editForm = {
      name: user.name ?? '',
      email: user.email ?? '',
      role: user.role ?? 'operator',
      pin: user.pin ?? '',
      image_src: user.image_src ?? '',
      password: '',
      password_confirmation: '',
    };
  }

  cancelEdit(): void {
    this.mode = 'create';
    this.editingUser = null;
    this.errorMessages = [];
    this.resetEditForm();
  }

  saveEdit(): void {
    if (!this.editingUser) {
      return;
    }

    this.errorMessages = [];

    const errors = this.validateEditUserForm();

    if (errors.length > 0) {
      this.errorMessages = errors;
      return;
    }

    const payload: any = {
      name: this.editForm.name.trim(),
      email: this.editForm.email.trim(),
      role: this.editForm.role,
      pin: this.editForm.pin.trim() || null,
      image_src: this.editForm.image_src.trim() || null,
    };

    if (this.editForm.password.trim()) {
      payload.password = this.editForm.password;
      payload.password_confirmation = this.editForm.password_confirmation;
    }

    const idToUpdate = String(this.editingUser.uuid ?? this.editingUser.id);

    this.userService.updateUser(idToUpdate, payload).subscribe({
      next: () => {
        this.cancelEdit();
        this.loadUsers();
        this.showSuccess('Usuario actualizado correctamente');
      },
      error: (error) => {
        this.errorMessages = this.extractBackendErrors(error);
      },
    });
  }

  private validateCreateUserForm(
    restaurantId: string | number | undefined,
  ): string[] {
    const errors: string[] = [];

    if (!restaurantId) {
      errors.push('No se ha encontrado el restaurant_id.');
    }

    if (!this.createForm.name.trim()) {
      errors.push('El nombre es obligatorio.');
    }

    if (!this.createForm.email.trim()) {
      errors.push('El email es obligatorio.');
    } else if (!this.isValidEmail(this.createForm.email.trim())) {
      errors.push('El email no tiene un formato válido.');
    }

    if (!this.createForm.password) {
      errors.push('La contraseña es obligatoria.');
    }

    if (!this.createForm.password_confirmation) {
      errors.push('Debes confirmar la contraseña.');
    }

    if (this.createForm.password && this.createForm.password.length < 8) {
      errors.push('La contraseña debe tener mínimo 8 caracteres.');
    }

    if (
      this.createForm.password &&
      this.createForm.password_confirmation &&
      this.createForm.password !== this.createForm.password_confirmation
    ) {
      errors.push('Las contraseñas no coinciden.');
    }

    if (!this.createForm.role) {
      errors.push('Debes seleccionar un rol.');
    }

    if (
      this.createForm.pin.trim() &&
      !/^\d+$/.test(this.createForm.pin.trim())
    ) {
      errors.push('El PIN solo puede contener números.');
    }

    return errors;
  }

  private validateEditUserForm(): string[] {
    const errors: string[] = [];

    if (!this.editForm.name.trim()) {
      errors.push('El nombre es obligatorio.');
    }

    if (!this.editForm.email.trim()) {
      errors.push('El email es obligatorio.');
    } else if (!this.isValidEmail(this.editForm.email.trim())) {
      errors.push('El email no tiene un formato válido.');
    }

    if (!this.editForm.role) {
      errors.push('Debes seleccionar un rol.');
    }

    if (this.editForm.pin.trim() && !/^\d+$/.test(this.editForm.pin.trim())) {
      errors.push('El PIN solo puede contener números.');
    }

    if (this.editForm.password || this.editForm.password_confirmation) {
      if (!this.editForm.password) {
        errors.push('Debes introducir la nueva contraseña.');
      }

      if (!this.editForm.password_confirmation) {
        errors.push('Debes confirmar la nueva contraseña.');
      }

      if (this.editForm.password && this.editForm.password.length < 8) {
        errors.push('La nueva contraseña debe tener mínimo 8 caracteres.');
      }

      if (
        this.editForm.password &&
        this.editForm.password_confirmation &&
        this.editForm.password !== this.editForm.password_confirmation
      ) {
        errors.push('Las nuevas contraseñas no coinciden.');
      }
    }

    return errors;
  }

  onSearchChange(): void {
    this.applyUserFilter();
  }

  applyUserFilter(): void {
    const term = this.searchTerm.trim().toLowerCase();

    if (!term) {
      this.filteredUsers = [...this.users];
      return;
    }

    this.filteredUsers = this.users.filter((user) => {
      const name = user.name?.toLowerCase() ?? '';
      const email = user.email?.toLowerCase() ?? '';

      return name.includes(term) || email.includes(term);
    });
  }

  clearSearch(): void {
    this.searchTerm = '';
    this.applyUserFilter();
  }

  async deleteUser(user: User): Promise<void> {
    const alert = await this.alertController.create({
      header: 'Eliminar usuario',
      message: `¿Seguro que quieres eliminar a "${user.name}"?`,
      cssClass: 'custom-dark-alert',
      mode: 'md',
      buttons: [
        {
          text: 'Cancelar',
          role: 'cancel',
        },
        {
          text: 'Eliminar',
          role: 'destructive',
          handler: () => {
            this.confirmDeleteUser(user);
          },
        },
      ],
    });

    await alert.present();
  }

  confirmDeleteUser(user: User): void {
    const idToDelete = String(user.uuid ?? user.id);

    this.userService.deleteUser(idToDelete).subscribe({
      next: () => {
        this.loadUsers();
      },
      error: () => {
        this.errorMessages = ['No se pudo eliminar el usuario.'];
      },
    });
  }

  resetCreateForm(): void {
    this.createForm = {
      name: '',
      email: '',
      password: '',
      password_confirmation: '',
      role: 'operator',
      pin: '',
      image_src: '',
    };
    this.errorMessages = [];
  }

  resetEditForm(): void {
    this.editForm = {
      name: '',
      email: '',
      role: 'operator',
      pin: '',
      image_src: '',
      password: '',
      password_confirmation: '',
    };
  }

  private isValidEmail(email: string): boolean {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  }

  private extractBackendErrors(error: any): string[] {
    const errors: string[] = [];

    if (error?.error?.errors) {
      Object.values(error.error.errors).forEach((messages) => {
        if (Array.isArray(messages)) {
          messages.forEach((message) => errors.push(String(message)));
        }
      });
    }

    if (errors.length > 0) {
      return errors;
    }

    if (error?.error?.message) {
      return [String(error.error.message)];
    }

    return ['Ha ocurrido un error inesperado.'];
  }

  async showSuccess(message: string): Promise<void> {
    const alert = await this.alertController.create({
      header: 'OK',
      message,
      cssClass: 'custom-dark-alert',
      mode: 'md',
      buttons: ['Aceptar'],
    });

    await alert.present();
  }
}
