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
import { ProductService, Product } from '../../services/api/product.service';
import { FamilyService, Family } from '../../services/api/family.service';
import { TaxService, Tax } from '../../services/api/tax.service';
import { AuthService } from '../../services/auth/auth.service';
import { ZoneService, Zone } from '../../services/api/zone.service';
import { TableService, TableItem } from '../../services/api/table.service';

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

  products: Product[] = [];
  filteredProducts: Product[] = [];
  productsLoading = false;
  productErrorMessages: string[] = [];
  productSearchTerm = '';

  productMode: 'create' | 'edit' = 'create';
  editingProduct: Product | null = null;

  families: Family[] = [];
  filteredFamilies: Family[] = [];
  familyErrorMessages: string[] = [];
  familySearchTerm = '';
  familyMode: 'create' | 'edit' = 'create';
  editingFamily: Family | null = null;

  createFamilyForm = {
    name: '',
    active: true,
  };

  editFamilyForm = {
    name: '',
    active: true,
  };

  taxes: Tax[] = [];
  filteredTaxes: Tax[] = [];
  taxErrorMessages: string[] = [];
  taxSearchTerm = '';
  taxMode: 'create' | 'edit' = 'create';
  editingTax: Tax | null = null;

  createTaxForm = {
    name: '',
    percentage: 21,
  };

  editTaxForm = {
    name: '',
    percentage: 21,
  };

  zones: Zone[] = [];
  filteredZones: Zone[] = [];
  zoneErrorMessages: string[] = [];
  zoneSearchTerm = '';
  zoneMode: 'create' | 'edit' = 'create';
  editingZone: Zone | null = null;

  createZoneForm = {
    name: '',
  };

  editZoneForm = {
    name: '',
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

  createProductForm = {
    name: '',
    family_id: '',
    tax_id: '',
    price: 0,
    stock: 0,
    image_src: '',
    active: true,
  };

  editProductForm = {
    name: '',
    family_id: '',
    tax_id: '',
    price: 0,
    stock: 0,
    image_src: '',
    active: true,
  };

  constructor(
    private userService: UserService,
    private productService: ProductService,
    private familyService: FamilyService,
    private taxService: TaxService,
    private zoneService: ZoneService,
    private tableService: TableService,
    private authService: AuthService,
    private alertController: AlertController,
  ) {}

  ngOnInit(): void {
    this.loadUsers();
  }

  selectSection(section: SettingsSection): void {
    this.selectedSection = section;
    this.errorMessages = [];
    this.productErrorMessages = [];
    this.familyErrorMessages = [];
    this.taxErrorMessages = [];
    this.tableErrorMessages = [];
    this.zoneErrorMessages = [];

    if (section === 'users') {
      this.loadUsers();
    }

    if (section === 'products') {
      this.loadFamilies();
      this.loadTaxes();
      this.loadProducts();
    }

    if (section === 'families') {
      this.loadFamilies();
    }

    if (section === 'taxes') {
      this.loadTaxes();
    }

    if (section === 'zones') {
      this.loadZones();
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

  loadFamilies(): void {
    const restaurantId = this.authService.getUser()?.restaurant_id;

    this.familyService.getFamilies().subscribe({
      next: (response: any) => {
        const families = Array.isArray(response)
          ? response
          : Array.isArray(response.data)
            ? response.data
            : (response.family ?? response.families ?? []);

        this.families = restaurantId
          ? families.filter(
              (family: Family) =>
                String(family.restaurant_id) === String(restaurantId),
            )
          : families;

        this.applyFamilyFilter();
      },
      error: () => {
        this.familyErrorMessages = ['No se pudieron cargar las familias.'];
      },
    });
  }

  createFamily(): void {
    const restaurantId = this.authService.getUser()?.restaurant_id;

    this.familyErrorMessages = [];

    const errors = this.validateCreateFamilyForm(restaurantId);

    if (errors.length > 0) {
      this.familyErrorMessages = errors;
      return;
    }

    const payload = {
      name: this.createFamilyForm.name.trim(),
      active: this.createFamilyForm.active,
      restaurant_id: restaurantId,
    };

    this.familyService.createFamily(payload).subscribe({
      next: () => {
        this.resetCreateFamilyForm();
        this.loadFamilies();
        this.showSuccess('Familia creada correctamente');
      },
      error: (error) => {
        this.familyErrorMessages = this.extractBackendErrors(error);
      },
    });
  }

  startEditFamily(family: Family): void {
    this.familyMode = 'edit';
    this.editingFamily = family;
    this.familyErrorMessages = [];

    this.editFamilyForm = {
      name: family.name ?? '',
      active: !!family.active,
    };
  }

  cancelEditFamily(): void {
    this.familyMode = 'create';
    this.editingFamily = null;
    this.familyErrorMessages = [];
    this.resetEditFamilyForm();
  }

  saveEditFamily(): void {
    if (!this.editingFamily) {
      return;
    }

    this.familyErrorMessages = [];

    const errors = this.validateEditFamilyForm();

    if (errors.length > 0) {
      this.familyErrorMessages = errors;
      return;
    }

    const payload = {
      name: this.editFamilyForm.name.trim(),
      active: this.editFamilyForm.active,
    };

    const idToUpdate = String(this.editingFamily.uuid ?? this.editingFamily.id);

    this.familyService.updateFamily(idToUpdate, payload).subscribe({
      next: () => {
        this.cancelEditFamily();
        this.loadFamilies();
        this.loadProducts();
        this.showSuccess('Familia actualizada correctamente');
      },
      error: (error) => {
        this.familyErrorMessages = this.extractBackendErrors(error);
      },
    });
  }

  private validateCreateFamilyForm(
    restaurantId: string | number | undefined,
  ): string[] {
    const errors: string[] = [];

    if (!restaurantId) {
      errors.push('No se ha encontrado el restaurant_id.');
    }

    if (!this.createFamilyForm.name.trim()) {
      errors.push('El nombre de la familia es obligatorio.');
    }

    return errors;
  }

  private validateEditFamilyForm(): string[] {
    const errors: string[] = [];

    if (!this.editFamilyForm.name.trim()) {
      errors.push('El nombre de la familia es obligatorio.');
    }

    return errors;
  }

  onFamilySearchChange(): void {
    this.applyFamilyFilter();
  }

  applyFamilyFilter(): void {
    const term = this.familySearchTerm.trim().toLowerCase();

    if (!term) {
      this.filteredFamilies = [...this.families];
      return;
    }

    this.filteredFamilies = this.families.filter((family) => {
      const name = family.name?.toLowerCase() ?? '';
      return name.includes(term);
    });
  }

  clearFamilySearch(): void {
    this.familySearchTerm = '';
    this.applyFamilyFilter();
  }

  async deleteFamily(family: Family): Promise<void> {
    const alert = await this.alertController.create({
      header: 'Eliminar familia',
      message: `¿Seguro que quieres eliminar "${family.name}"?`,
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
            this.confirmDeleteFamily(family);
          },
        },
      ],
    });

    await alert.present();
  }

  confirmDeleteFamily(family: Family): void {
    const idToDelete = String(family.uuid ?? family.id);

    this.familyService.deleteFamily(idToDelete).subscribe({
      next: () => {
        this.loadFamilies();
        this.loadProducts();
      },
      error: () => {
        this.familyErrorMessages = ['No se pudo eliminar la familia.'];
      },
    });
  }

  resetCreateFamilyForm(): void {
    this.createFamilyForm = {
      name: '',
      active: true,
    };
    this.familyErrorMessages = [];
  }

  resetEditFamilyForm(): void {
    this.editFamilyForm = {
      name: '',
      active: true,
    };
  }

  loadTaxes(): void {
    const restaurantId = this.authService.getUser()?.restaurant_id;

    this.taxService.getTaxes().subscribe({
      next: (response: any) => {
        const taxes = Array.isArray(response)
          ? response
          : Array.isArray(response.data)
            ? response.data
            : (response.tax ?? response.taxes ?? []);

        this.taxes = restaurantId
          ? taxes.filter(
              (tax: Tax) => String(tax.restaurant_id) === String(restaurantId),
            )
          : taxes;

        this.applyTaxFilter();
      },
      error: () => {
        this.taxErrorMessages = ['No se pudieron cargar los impuestos.'];
      },
    });
  }

  createTax(): void {
    const restaurantId = this.authService.getUser()?.restaurant_id;

    this.taxErrorMessages = [];

    const errors = this.validateCreateTaxForm(restaurantId);

    if (errors.length > 0) {
      this.taxErrorMessages = errors;
      return;
    }

    const payload = {
      name: this.createTaxForm.name.trim(),
      percentage: Number(this.createTaxForm.percentage),
      restaurant_id: restaurantId,
    };

    this.taxService.createTax(payload).subscribe({
      next: () => {
        this.resetCreateTaxForm();
        this.loadTaxes();
        this.showSuccess('Impuesto creado correctamente');
      },
      error: (error) => {
        this.taxErrorMessages = this.extractBackendErrors(error);
      },
    });
  }

  startEditTax(tax: Tax): void {
    this.taxMode = 'edit';
    this.editingTax = tax;
    this.taxErrorMessages = [];

    this.editTaxForm = {
      name: tax.name ?? '',
      percentage: Number(tax.percentage ?? 0),
    };
  }

  cancelEditTax(): void {
    this.taxMode = 'create';
    this.editingTax = null;
    this.taxErrorMessages = [];
    this.resetEditTaxForm();
  }

  saveEditTax(): void {
    if (!this.editingTax) {
      return;
    }

    this.taxErrorMessages = [];

    const errors = this.validateEditTaxForm();

    if (errors.length > 0) {
      this.taxErrorMessages = errors;
      return;
    }

    const payload = {
      name: this.editTaxForm.name.trim(),
      percentage: Number(this.editTaxForm.percentage),
    };

    const idToUpdate = String(this.editingTax.uuid ?? this.editingTax.id);

    this.taxService.updateTax(idToUpdate, payload).subscribe({
      next: () => {
        this.cancelEditTax();
        this.loadTaxes();
        this.loadProducts();
        this.showSuccess('Impuesto actualizado correctamente');
      },
      error: (error) => {
        this.taxErrorMessages = this.extractBackendErrors(error);
      },
    });
  }

  private validateCreateTaxForm(
    restaurantId: string | number | undefined,
  ): string[] {
    const errors: string[] = [];

    if (!restaurantId) {
      errors.push('No se ha encontrado el restaurant_id.');
    }

    if (!this.createTaxForm.name.trim()) {
      errors.push('El nombre del impuesto es obligatorio.');
    }

    if (
      this.createTaxForm.percentage === null ||
      this.createTaxForm.percentage === undefined
    ) {
      errors.push('El porcentaje es obligatorio.');
    }

    if (
      isNaN(Number(this.createTaxForm.percentage)) ||
      Number(this.createTaxForm.percentage) < 0 ||
      Number(this.createTaxForm.percentage) > 100
    ) {
      errors.push('El porcentaje debe estar entre 0 y 100.');
    }

    return errors;
  }

  private validateEditTaxForm(): string[] {
    const errors: string[] = [];

    if (!this.editTaxForm.name.trim()) {
      errors.push('El nombre del impuesto es obligatorio.');
    }

    if (
      isNaN(Number(this.editTaxForm.percentage)) ||
      Number(this.editTaxForm.percentage) < 0 ||
      Number(this.editTaxForm.percentage) > 100
    ) {
      errors.push('El porcentaje debe estar entre 0 y 100.');
    }

    return errors;
  }

  onTaxSearchChange(): void {
    this.applyTaxFilter();
  }

  applyTaxFilter(): void {
    const term = this.taxSearchTerm.trim().toLowerCase();

    if (!term) {
      this.filteredTaxes = [...this.taxes];
      return;
    }

    this.filteredTaxes = this.taxes.filter((tax) => {
      const name = tax.name?.toLowerCase() ?? '';
      return name.includes(term);
    });
  }

  clearTaxSearch(): void {
    this.taxSearchTerm = '';
    this.applyTaxFilter();
  }

  async deleteTax(tax: Tax): Promise<void> {
    const alert = await this.alertController.create({
      header: 'Eliminar impuesto',
      message: `¿Seguro que quieres eliminar "${tax.name}"?`,
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
            this.confirmDeleteTax(tax);
          },
        },
      ],
    });

    await alert.present();
  }

  confirmDeleteTax(tax: Tax): void {
    const idToDelete = String(tax.uuid ?? tax.id);

    this.taxService.deleteTax(idToDelete).subscribe({
      next: () => {
        this.loadTaxes();
        this.loadProducts();
      },
      error: () => {
        this.taxErrorMessages = ['No se pudo eliminar el impuesto.'];
      },
    });
  }

  resetCreateTaxForm(): void {
    this.createTaxForm = {
      name: '',
      percentage: 21,
    };
    this.taxErrorMessages = [];
  }

  resetEditTaxForm(): void {
    this.editTaxForm = {
      name: '',
      percentage: 21,
    };
  }

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
        console.table(this.zones);

        this.applyZoneFilter();
      },

      error: () => {
        this.zoneErrorMessages = ['No se pudieron cargar las zonas.'];
      },
    });
  }

  createZone(): void {
    const restaurantId = this.authService.getUser()?.restaurant_id;

    this.zoneErrorMessages = [];

    const errors = this.validateCreateZoneForm(restaurantId);

    if (errors.length > 0) {
      this.zoneErrorMessages = errors;
      return;
    }

    const payload = {
      name: this.createZoneForm.name.trim(),
      restaurant_id: restaurantId,
    };

    this.zoneService.createZone(payload).subscribe({
      next: () => {
        this.resetCreateZoneForm();
        this.loadZones();
        this.showSuccess('Zona creada correctamente');
      },
      error: (error) => {
        this.zoneErrorMessages = this.extractBackendErrors(error);
      },
    });
  }

  startEditZone(zone: Zone): void {
    this.zoneMode = 'edit';
    this.editingZone = zone;
    this.zoneErrorMessages = [];

    this.editZoneForm = {
      name: zone.name ?? '',
    };
  }

  cancelEditZone(): void {
    this.zoneMode = 'create';
    this.editingZone = null;
    this.zoneErrorMessages = [];
    this.resetEditZoneForm();
  }

  saveEditZone(): void {
    if (!this.editingZone) {
      return;
    }

    this.zoneErrorMessages = [];

    const errors = this.validateEditZoneForm();

    if (errors.length > 0) {
      this.zoneErrorMessages = errors;
      return;
    }

    const payload = {
      name: this.editZoneForm.name.trim(),
    };

    const idToUpdate = String(this.editingZone.uuid ?? this.editingZone.id);

    this.zoneService.updateZone(idToUpdate, payload).subscribe({
      next: () => {
        this.cancelEditZone();
        this.loadZones();
        this.showSuccess('Zona actualizada correctamente');
      },
      error: (error) => {
        this.zoneErrorMessages = this.extractBackendErrors(error);
      },
    });
  }

  private validateCreateZoneForm(
    restaurantId: string | number | undefined,
  ): string[] {
    const errors: string[] = [];

    if (!restaurantId) {
      errors.push('No se ha encontrado el restaurant_id.');
    }

    if (!this.createZoneForm.name.trim()) {
      errors.push('El nombre de la zona es obligatorio.');
    }

    return errors;
  }

  private validateEditZoneForm(): string[] {
    const errors: string[] = [];

    if (!this.editZoneForm.name.trim()) {
      errors.push('El nombre de la zona es obligatorio.');
    }

    return errors;
  }

  onZoneSearchChange(): void {
    this.applyZoneFilter();
  }

  applyZoneFilter(): void {
    const term = this.zoneSearchTerm.trim().toLowerCase();

    if (!term) {
      this.filteredZones = [...this.zones];
      return;
    }

    this.filteredZones = this.zones.filter((zone) => {
      const name = zone.name?.toLowerCase() ?? '';
      return name.includes(term);
    });
  }

  clearZoneSearch(): void {
    this.zoneSearchTerm = '';
    this.applyZoneFilter();
  }

  async deleteZone(zone: Zone): Promise<void> {
    const alert = await this.alertController.create({
      header: 'Eliminar zona',
      message: `¿Seguro que quieres eliminar "${zone.name}"?`,
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
            this.confirmDeleteZone(zone);
          },
        },
      ],
    });

    await alert.present();
  }

  confirmDeleteZone(zone: Zone): void {
    const idToDelete = String(zone.uuid ?? zone.id);

    this.zoneService.deleteZone(idToDelete).subscribe({
      next: () => {
        this.loadZones();
      },
      error: () => {
        this.zoneErrorMessages = ['No se pudo eliminar la zona.'];
      },
    });
  }

  resetCreateZoneForm(): void {
    this.createZoneForm = {
      name: '',
    };
    this.zoneErrorMessages = [];
  }

  resetEditZoneForm(): void {
    this.editZoneForm = {
      name: '',
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
  //cambios
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

  loadProducts(): void {
    this.productsLoading = true;
    this.productErrorMessages = [];

    const restaurantId = this.authService.getUser()?.restaurant_id;

    this.productService.getProducts().subscribe({
      next: (response: any) => {
        const products = Array.isArray(response)
          ? response
          : Array.isArray(response.data)
            ? response.data
            : (response.products ?? []);

        this.products = restaurantId
          ? products.filter(
              (product: Product) =>
                String(product.restaurant_id) === String(restaurantId),
            )
          : products;

        this.applyProductFilter();
        this.productsLoading = false;
      },
      error: () => {
        this.productErrorMessages = ['No se pudieron cargar los productos.'];
        this.productsLoading = false;
      },
    });
  }

  createProduct(): void {
    const restaurantId = this.authService.getUser()?.restaurant_id;

    this.productErrorMessages = [];

    const errors = this.validateCreateProductForm(restaurantId);

    if (errors.length > 0) {
      this.productErrorMessages = errors;
      return;
    }

    const payload = {
      name: this.createProductForm.name.trim(),
      family_id: this.createProductForm.family_id,
      tax_id: this.createProductForm.tax_id,
      price: Math.round(Number(this.createProductForm.price) * 100),
      stock: Number(this.createProductForm.stock),
      image_src: this.createProductForm.image_src.trim() || null,
      active: this.createProductForm.active,
      restaurant_id: restaurantId,
    };

    this.productService.createProduct(payload).subscribe({
      next: () => {
        this.resetCreateProductForm();
        this.loadProducts();
        this.showSuccess('Producto creado correctamente');
      },
      error: (error) => {
        this.productErrorMessages = this.extractBackendErrors(error);
      },
    });
  }

  startEditProduct(product: Product): void {
    this.productMode = 'edit';
    this.editingProduct = product;
    this.productErrorMessages = [];

    this.editProductForm = {
      name: product.name ?? '',
      family_id: String(product.family_id ?? ''),
      tax_id: String(product.tax_id ?? ''),
      price: Number(product.price ?? 0) / 100,
      stock: Number(product.stock ?? 0),
      image_src: product.image_src ?? '',
      active: !!product.active,
    };
  }

  cancelEditProduct(): void {
    this.productMode = 'create';
    this.editingProduct = null;
    this.productErrorMessages = [];
    this.resetEditProductForm();
  }

  saveEditProduct(): void {
    if (!this.editingProduct) {
      return;
    }

    this.productErrorMessages = [];

    const errors = this.validateEditProductForm();

    if (errors.length > 0) {
      this.productErrorMessages = errors;
      return;
    }

    const payload = {
      name: this.editProductForm.name.trim(),
      family_id: this.editProductForm.family_id,
      tax_id: this.editProductForm.tax_id,
      price: Math.round(Number(this.editProductForm.price) * 100),
      stock: Number(this.editProductForm.stock),
      image_src: this.editProductForm.image_src.trim() || null,
      active: this.editProductForm.active,
    };

    const idToUpdate = String(
      this.editingProduct.uuid ?? this.editingProduct.id,
    );

    this.productService.updateProduct(idToUpdate, payload).subscribe({
      next: () => {
        this.cancelEditProduct();
        this.loadProducts();
        this.showSuccess('Producto actualizado correctamente');
      },
      error: (error) => {
        this.productErrorMessages = this.extractBackendErrors(error);
      },
    });
  }

  private validateCreateProductForm(
    restaurantId: string | number | undefined,
  ): string[] {
    const errors: string[] = [];

    if (!restaurantId) {
      errors.push('No se ha encontrado el restaurant_id.');
    }

    if (!this.createProductForm.name.trim()) {
      errors.push('El nombre del producto es obligatorio.');
    }

    if (!this.createProductForm.family_id) {
      errors.push('Debes seleccionar una familia.');
    }

    if (!this.createProductForm.tax_id) {
      errors.push('Debes seleccionar un impuesto.');
    }

    if (
      Number(this.createProductForm.price) < 0 ||
      isNaN(Number(this.createProductForm.price))
    ) {
      errors.push('El precio debe ser válido y mayor o igual a 0.');
    }

    if (
      Number(this.createProductForm.stock) < 0 ||
      isNaN(Number(this.createProductForm.stock))
    ) {
      errors.push('El stock debe ser válido y mayor o igual a 0.');
    }

    return errors;
  }

  private validateEditProductForm(): string[] {
    const errors: string[] = [];

    if (!this.editProductForm.name.trim()) {
      errors.push('El nombre del producto es obligatorio.');
    }

    if (!this.editProductForm.family_id) {
      errors.push('Debes seleccionar una familia.');
    }

    if (!this.editProductForm.tax_id) {
      errors.push('Debes seleccionar un impuesto.');
    }

    if (
      Number(this.editProductForm.price) < 0 ||
      isNaN(Number(this.editProductForm.price))
    ) {
      errors.push('El precio debe ser válido y mayor o igual a 0.');
    }

    if (
      Number(this.editProductForm.stock) < 0 ||
      isNaN(Number(this.editProductForm.stock))
    ) {
      errors.push('El stock debe ser válido y mayor o igual a 0.');
    }

    return errors;
  }

  onProductSearchChange(): void {
    this.applyProductFilter();
  }

  applyProductFilter(): void {
    const term = this.productSearchTerm.trim().toLowerCase();

    if (!term) {
      this.filteredProducts = [...this.products];
      return;
    }

    this.filteredProducts = this.products.filter((product) => {
      const name = product.name?.toLowerCase() ?? '';
      return name.includes(term);
    });
  }

  clearProductSearch(): void {
    this.productSearchTerm = '';
    this.applyProductFilter();
  }

  async deleteProduct(product: Product): Promise<void> {
    const alert = await this.alertController.create({
      header: 'Eliminar producto',
      message: `¿Seguro que quieres eliminar "${product.name}"?`,
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
            this.confirmDeleteProduct(product);
          },
        },
      ],
    });

    await alert.present();
  }

  confirmDeleteProduct(product: Product): void {
    const idToDelete = String(product.uuid ?? product.id);

    this.productService.deleteProduct(idToDelete).subscribe({
      next: () => {
        this.loadProducts();
      },
      error: () => {
        this.productErrorMessages = ['No se pudo eliminar el producto.'];
      },
    });
  }

  resetCreateProductForm(): void {
    this.createProductForm = {
      name: '',
      family_id: '',
      tax_id: '',
      price: 0,
      stock: 0,
      image_src: '',
      active: true,
    };
    this.productErrorMessages = [];
  }

  resetEditProductForm(): void {
    this.editProductForm = {
      name: '',
      family_id: '',
      tax_id: '',
      price: 0,
      stock: 0,
      image_src: '',
      active: true,
    };
  }

  getFamilyName(familyId: string | number): string {
    const family = this.families.find(
      (item) =>
        String(item.id) === String(familyId) ||
        String(item.uuid) === String(familyId),
    );

    return family?.name ?? `Familia ${familyId}`;
  }

  getTaxName(taxId: string | number): string {
    const tax = this.taxes.find(
      (item) =>
        String(item.id) === String(taxId) ||
        String(item.uuid) === String(taxId),
    );

    return tax?.name ?? `Impuesto ${taxId}`;
  }

  formatPrice(cents: number): string {
    return (Number(cents) / 100).toFixed(2) + ' €';
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
