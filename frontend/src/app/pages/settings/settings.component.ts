import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import {
  IonContent,
  IonCard,
  IonCardContent,
  IonButton,
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

  // ---------------- USERS ----------------

  loadUsers(): void {
    this.loading = true;
    this.errorMessages = [];

    const loggedUser = this.authService.getUser();
    const restaurantId = loggedUser?.restaurant_id;

    this.userService.getUsers().subscribe({
      next: (response: any) => {
        const users = Array.isArray(response)
          ? response
          : Array.isArray(response.data)
            ? response.data
            : response.users ?? [];

        this.users = restaurantId
          ? users.filter((user: User) => String(user.restaurant_id) === String(restaurantId))
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
    const loggedUser = this.authService.getUser();
    const restaurantId = loggedUser?.restaurant_id;

    this.errorMessages = [];

    const frontendErrors = this.validateCreateForm(restaurantId);

    if (frontendErrors.length > 0) {
      this.errorMessages = frontendErrors;
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

    const frontendErrors = this.validateEditForm();

    if (frontendErrors.length > 0) {
      this.errorMessages = frontendErrors;
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

  private validateCreateForm(restaurantId: string | number | undefined): string[] {
    const errors: string[] = [];
    const email = this.createForm.email.trim();
    const password = this.createForm.password;
    const passwordConfirmation = this.createForm.password_confirmation;
    const pin = this.createForm.pin.trim();

    if (!restaurantId) {
      errors.push('No se ha encontrado el restaurant_id.');
    }

    if (!this.createForm.name.trim()) {
      errors.push('El nombre es obligatorio.');
    }

    if (!email) {
      errors.push('El email es obligatorio.');
    } else if (!this.isValidEmail(email)) {
      errors.push('El email no tiene un formato válido.');
    }

    if (!password) {
      errors.push('La contraseña es obligatoria.');
    }

    if (!passwordConfirmation) {
      errors.push('Debes confirmar la contraseña.');
    }

    if (password && password.length < 8) {
      errors.push('La contraseña debe tener mínimo 8 caracteres.');
    }

    if (password && passwordConfirmation && password !== passwordConfirmation) {
      errors.push('Las contraseñas no coinciden.');
    }

    if (!this.createForm.role) {
      errors.push('Debes seleccionar un rol.');
    }

    if (pin && !/^\d+$/.test(pin)) {
      errors.push('El PIN solo puede contener números.');
    }

    return errors;
  }

  private validateEditForm(): string[] {
    const errors: string[] = [];
    const email = this.editForm.email.trim();
    const pin = this.editForm.pin.trim();
    const password = this.editForm.password;
    const passwordConfirmation = this.editForm.password_confirmation;

    if (!this.editForm.name.trim()) {
      errors.push('El nombre es obligatorio.');
    }

    if (!email) {
      errors.push('El email es obligatorio.');
    } else if (!this.isValidEmail(email)) {
      errors.push('El email no tiene un formato válido.');
    }

    if (!this.editForm.role) {
      errors.push('Debes seleccionar un rol.');
    }

    if (pin && !/^\d+$/.test(pin)) {
      errors.push('El PIN solo puede contener números.');
    }

    if (password || passwordConfirmation) {
      if (!password) {
        errors.push('Debes introducir la nueva contraseña.');
      }

      if (!passwordConfirmation) {
        errors.push('Debes confirmar la nueva contraseña.');
      }

      if (password && password.length < 8) {
        errors.push('La nueva contraseña debe tener mínimo 8 caracteres.');
      }

      if (password && passwordConfirmation && password !== passwordConfirmation) {
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
            this.confirmDelete(user);
          },
        },
      ],
    });

    await alert.present();
  }

  confirmDelete(user: User): void {
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

  // ---------------- PRODUCTS ----------------

  loadFamilies(): void {
    const loggedUser = this.authService.getUser();
    const restaurantId = loggedUser?.restaurant_id;

    this.familyService.getFamilies().subscribe({
      next: (response: any) => {
        const families = Array.isArray(response)
          ? response
          : Array.isArray(response.data)
            ? response.data
            : response.family ?? response.families ?? [];

        this.families = restaurantId
          ? families.filter((family: Family) => String(family.restaurant_id) === String(restaurantId))
          : families;
      },
      error: () => {
        this.productErrorMessages = ['No se pudieron cargar las familias.'];
      },
    });
  }

  loadTaxes(): void {
    const loggedUser = this.authService.getUser();
    const restaurantId = loggedUser?.restaurant_id;

    this.taxService.getTaxes().subscribe({
      next: (response: any) => {
        const taxes = Array.isArray(response)
          ? response
          : Array.isArray(response.data)
            ? response.data
            : response.tax ?? response.taxes ?? [];

        this.taxes = restaurantId
          ? taxes.filter((tax: Tax) => String(tax.restaurant_id) === String(restaurantId))
          : taxes;
      },
      error: () => {
        this.productErrorMessages = ['No se pudieron cargar los impuestos.'];
      },
    });
  }

  loadProducts(): void {
    this.productsLoading = true;
    this.productErrorMessages = [];

    const loggedUser = this.authService.getUser();
    const restaurantId = loggedUser?.restaurant_id;

    this.productService.getProducts().subscribe({
      next: (response: any) => {
        const products = Array.isArray(response)
          ? response
          : Array.isArray(response.data)
            ? response.data
            : response.products ?? [];

        this.products = restaurantId
          ? products.filter((product: Product) => String(product.restaurant_id) === String(restaurantId))
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
    const loggedUser = this.authService.getUser();
    const restaurantId = loggedUser?.restaurant_id;

    this.productErrorMessages = [];

    const frontendErrors = this.validateCreateProductForm(restaurantId);

    if (frontendErrors.length > 0) {
      this.productErrorMessages = frontendErrors;
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

    const frontendErrors = this.validateEditProductForm();

    if (frontendErrors.length > 0) {
      this.productErrorMessages = frontendErrors;
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

    const idToUpdate = String(this.editingProduct.uuid ?? this.editingProduct.id);

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

  private validateCreateProductForm(restaurantId: string | number | undefined): string[] {
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

    if (Number(this.createProductForm.price) < 0 || isNaN(Number(this.createProductForm.price))) {
      errors.push('El precio debe ser válido y mayor o igual a 0.');
    }

    if (Number(this.createProductForm.stock) < 0 || isNaN(Number(this.createProductForm.stock))) {
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

    if (Number(this.editProductForm.price) < 0 || isNaN(Number(this.editProductForm.price))) {
      errors.push('El precio debe ser válido y mayor o igual a 0.');
    }

    if (Number(this.editProductForm.stock) < 0 || isNaN(Number(this.editProductForm.stock))) {
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
      (item) => String(item.id) === String(familyId) || String(item.uuid) === String(familyId)
    );

    return family?.name ?? `Familia ${familyId}`;
  }

  getTaxName(taxId: string | number): string {
    const tax = this.taxes.find(
      (item) => String(item.id) === String(taxId) || String(item.uuid) === String(taxId)
    );

    return tax?.name ?? `Impuesto ${taxId}`;
  }

  formatPrice(cents: number): string {
    return (Number(cents) / 100).toFixed(2) + ' €';
  }

  // ---------------- COMMON ----------------

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