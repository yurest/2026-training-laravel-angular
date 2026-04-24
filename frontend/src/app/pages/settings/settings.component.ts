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

  // USERS
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

  // PRODUCTS
  products: Product[] = [];
  filteredProducts: Product[] = [];
  productsLoading = false;
  productErrorMessages: string[] = [];
  productSearchTerm = '';
  productMode: 'create' | 'edit' = 'create';
  editingProduct: Product | null = null;

  families: Family[] = [];
  taxes: Tax[] = [];

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
    private authService: AuthService,
    private alertController: AlertController,
  ) {}

  ngOnInit(): void {
    this.loadUsers();
  }

  // ---------- SECTIONS ----------
  selectSection(section: SettingsSection): void {
    this.selectedSection = section;
    this.errorMessages = [];
    this.productErrorMessages = [];

    if (section === 'users') {
      this.loadUsers();
    }

    if (section === 'products') {
      this.loadFamilies();
      this.loadTaxes();
      this.loadProducts();
    }
  }

  isSectionActive(section: SettingsSection): boolean {
    return this.selectedSection === section;
  }

  // ---------- USERS ----------
  loadUsers(): void {
    this.loading = true;

    const loggedUser = this.authService.getUser();
    const restaurantId = loggedUser?.restaurant_id;

    this.userService.getUsers().subscribe({
      next: (response: any) => {
        const users = Array.isArray(response)
          ? response
          : response.data ?? response.users ?? [];

        this.users = restaurantId
          ? users.filter((u: User) => String(u.restaurant_id) === String(restaurantId))
          : users;

        this.applyUserFilter();
        this.loading = false;
      },
      error: () => {
        this.errorMessages = ['Error cargando usuarios'];
        this.loading = false;
      },
    });
  }

  createUser(): void {
    const restaurantId = this.authService.getUser()?.restaurant_id;

    this.errorMessages = [];

    const payload = {
      ...this.createForm,
      restaurant_id: restaurantId,
    };

    this.userService.createUser(payload).subscribe({
      next: () => {
        this.resetCreateForm();
        this.loadUsers();
        this.showSuccess('Usuario creado');
      },
      error: (error) => {
        this.errorMessages = this.extractBackendErrors(error);
      },
    });
  }

  startEdit(user: User): void {
    this.mode = 'edit';
    this.editingUser = user;

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
    this.resetEditForm();
  }

  saveEdit(): void {
    if (!this.editingUser) return;

    const payload: any = {
      name: this.editForm.name,
      email: this.editForm.email,
      role: this.editForm.role,
      pin: this.editForm.pin,
      image_src: this.editForm.image_src,
    };

    if (this.editForm.password) {
      payload.password = this.editForm.password;
      payload.password_confirmation = this.editForm.password_confirmation;
    }

    const id = String(this.editingUser.uuid ?? this.editingUser.id);

    this.userService.updateUser(id, payload).subscribe({
      next: () => {
        this.cancelEdit();
        this.loadUsers();
        this.showSuccess('Usuario actualizado');
      },
      error: (error) => {
        this.errorMessages = this.extractBackendErrors(error);
      },
    });
  }

  async deleteUser(user: User) {
    const alert = await this.alertController.create({
      header: 'Eliminar usuario',
      message: `¿Seguro que quieres eliminar a "${user.name}"?`,
      cssClass: 'custom-dark-alert',
      buttons: [
        { text: 'Cancelar', role: 'cancel' },
        {
          text: 'Eliminar',
          role: 'destructive',
          handler: () => {
            const id = String(user.uuid ?? user.id);
            this.userService.deleteUser(id).subscribe(() => this.loadUsers());
          },
        },
      ],
    });

    await alert.present();
  }

  applyUserFilter(): void {
    const term = this.searchTerm.toLowerCase();
    this.filteredUsers = this.users.filter(
      (u) =>
        u.name?.toLowerCase().includes(term) ||
        u.email?.toLowerCase().includes(term)
    );
  }

  clearSearch(): void {
    this.searchTerm = '';
    this.applyUserFilter();
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

  // ---------- COMMON ----------
  private extractBackendErrors(error: any): string[] {
    if (error?.error?.errors) {
      return Object.values(error.error.errors).flat() as string[];
    }
    return [error?.error?.message || 'Error'];
  }

  async showSuccess(message: string) {
    const alert = await this.alertController.create({
      header: 'OK',
      message,
      cssClass: 'custom-dark-alert',
      buttons: ['Aceptar'],
    });
    await alert.present();
  }
}