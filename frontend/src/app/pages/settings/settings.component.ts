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
import { TableService, TableItem } from '../../services/api/table.service';
import { ProductsSettingsComponent } from './components/products-settings/products-settings.component';
import { FamiliesSettingsComponent } from './components/families-settings/families-settings.component';
import { TaxesSettingsComponent } from './components/taxes-settings/taxes-settings.component';
import { ZonesSettingsComponent } from './components/zones-settings/zones-settings.component';
import { ZoneService, Zone } from '../../services/api/zone.service';

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

  tables: TableItem[] = [];
  filteredTables: TableItem[] = [];
  tableErrorMessages: string[] = [];
  tableSearchTerm = '';
  tableMode: 'create' | 'edit' = 'create';
  editingTable: TableItem | null = null;

  createTableForm = {
    name: '',
    zone_id: '',
  };

  editTableForm = {
    name: '',
    zone_id: '',
  };
 zones: Zone[] = [];


  constructor(
    private userService: UserService,
    private tableService: TableService,
    private authService: AuthService,
    private alertController: AlertController,
      private zoneService: ZoneService,
  ) {}

  ngOnInit(): void {
    this.loadUsers();
  }

  selectSection(section: SettingsSection): void {
    this.selectedSection = section;
    this.errorMessages = [];
    this.tableErrorMessages = [];

    if (section === 'users') {
      this.loadUsers();
    }


    if (section === 'tables') {
      this.loadZones();
      this.loadTables();
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

  loadTables(): void {
    const restaurantId = this.authService.getUser()?.restaurant_id;

    this.tableService.getTables().subscribe({
      next: (response: any) => {
        const tables = Array.isArray(response)
          ? response
          : Array.isArray(response.data)
            ? response.data
            : (response.table ?? response.tables ?? []);

        this.tables = restaurantId
          ? tables.filter(
              (table: TableItem) =>
                String(table.restaurant_id) === String(restaurantId),
            )
          : tables;

        this.applyTableFilter();
      },
      error: () => {
        this.tableErrorMessages = ['No se pudieron cargar las mesas.'];
      },
    });
  }

  // =====================
  // TABLE METHODS
  // =====================
loadZones(): void {
  const restaurantId = this.authService.getUser()?.restaurant_id;

  this.zoneService.getZones().subscribe({
    next: (response: any) => {
      const zones = Array.isArray(response)
        ? response
        : Array.isArray(response.data)
          ? response.data
          : (response.zone ?? response.zones ?? []);

      this.zones = restaurantId
        ? zones.filter(
            (zone: Zone) =>
              String(zone.restaurant_id) === String(restaurantId),
          )
        : zones;
    },
    error: () => {
      this.tableErrorMessages = ['No se pudieron cargar las zonas.'];
    },
  });
}



  createTable(): void {
    const restaurantId = this.authService.getUser()?.restaurant_id;

    this.tableErrorMessages = [];

    const errors = this.validateCreateTableForm(restaurantId);

    if (errors.length > 0) {
      this.tableErrorMessages = errors;
      return;
    }

    const payload = {
      name: this.createTableForm.name.trim(),
      zone_id: this.createTableForm.zone_id,
      restaurant_id: restaurantId,
    };
    console.log('PAYLOAD CREATE TABLE:', payload);

    this.tableService.createTable(payload).subscribe({
      next: () => {
        this.resetCreateTableForm();
        this.loadTables();
        this.showSuccess('Mesa creada correctamente');
      },
      error: (error) => {
        console.log('ERROR CREATE TABLE:', error);
        this.tableErrorMessages = this.extractBackendErrors(error);
      },
    });
  }

  startEditTable(table: TableItem): void {
    this.tableMode = 'edit';
    this.editingTable = table;
    this.tableErrorMessages = [];

    this.editTableForm = {
      name: table.name ?? '',
      zone_id: String(table.zone_id ?? ''),
    };
  }

  cancelEditTable(): void {
    this.tableMode = 'create';
    this.editingTable = null;
    this.tableErrorMessages = [];
    this.resetEditTableForm();
  }

  saveEditTable(): void {
    if (!this.editingTable) {
      return;
    }

    this.tableErrorMessages = [];

    const errors = this.validateEditTableForm();

    if (errors.length > 0) {
      this.tableErrorMessages = errors;
      return;
    }

    const payload = {
      name: this.editTableForm.name.trim(),
      zone_id: this.editTableForm.zone_id,
    };

    const idToUpdate = String(this.editingTable.uuid ?? this.editingTable.id);

    this.tableService.updateTable(idToUpdate, payload).subscribe({
      next: () => {
        this.cancelEditTable();
        this.loadTables();
        this.showSuccess('Mesa actualizada correctamente');
      },
      error: (error) => {
        this.tableErrorMessages = this.extractBackendErrors(error);
      },
    });
  }

  private validateCreateTableForm(
    restaurantId: string | number | undefined,
  ): string[] {
    const errors: string[] = [];

    if (!restaurantId) {
      errors.push('No se ha encontrado el restaurant_id.');
    }

    if (!this.createTableForm.name.trim()) {
      errors.push('El nombre de la mesa es obligatorio.');
    }

    if (!this.createTableForm.zone_id) {
      errors.push('Debes seleccionar una zona.');
    }

    return errors;
  }

  private validateEditTableForm(): string[] {
    const errors: string[] = [];

    if (!this.editTableForm.name.trim()) {
      errors.push('El nombre de la mesa es obligatorio.');
    }

    if (!this.editTableForm.zone_id) {
      errors.push('Debes seleccionar una zona.');
    }

    return errors;
  }

  onTableSearchChange(): void {
    this.applyTableFilter();
  }

  applyTableFilter(): void {
    const term = this.tableSearchTerm.trim().toLowerCase();

    if (!term) {
      this.filteredTables = [...this.tables];
      return;
    }

    this.filteredTables = this.tables.filter((table) => {
      const name = table.name?.toLowerCase() ?? '';
      return name.includes(term);
    });
  }

  clearTableSearch(): void {
    this.tableSearchTerm = '';
    this.applyTableFilter();
  }

  async deleteTable(table: TableItem): Promise<void> {
    const alert = await this.alertController.create({
      header: 'Eliminar mesa',
      message: `¿Seguro que quieres eliminar "${table.name}"?`,
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
            this.confirmDeleteTable(table);
          },
        },
      ],
    });

    await alert.present();
  }

  confirmDeleteTable(table: TableItem): void {
    const idToDelete = String(table.uuid ?? table.id);

    this.tableService.deleteTable(idToDelete).subscribe({
      next: () => {
        this.loadTables();
      },
      error: () => {
        this.tableErrorMessages = ['No se pudo eliminar la mesa.'];
      },
    });
  }

  resetCreateTableForm(): void {
    this.createTableForm = {
      name: '',
      zone_id: '',
    };
    this.tableErrorMessages = [];
  }

  resetEditTableForm(): void {
    this.editTableForm = {
      name: '',
      zone_id: '',
    };
  }

  getZoneName(zoneId: string | number): string {
    const zone = this.zones.find(
      (item) =>
        String(item.id) === String(zoneId) ||
        String(item.uuid) === String(zoneId),
    );

    return zone?.name ?? `Zona ${zoneId}`;
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
