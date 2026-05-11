import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import {
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
import { Product, ProductService } from '../../../../services/api/product.service';
import { Family, FamilyService } from '../../../../services/api/family.service';
import { Tax, TaxService } from '../../../../services/api/tax.service';
import { AuthService } from '../../../../services/auth/auth.service';

@Component({
  selector: 'app-products-settings',
  standalone: true,
  templateUrl: './products-settings.component.html',
  styleUrls: ['./products-settings.component.scss'],
  imports: [
    CommonModule,
    FormsModule,
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
export class ProductsSettingsComponent implements OnInit {
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
    private productService: ProductService,
    private familyService: FamilyService,
    private taxService: TaxService,
    private authService: AuthService,
    private alertController: AlertController,
  ) {}

  ngOnInit(): void {
    this.loadFamilies();
    this.loadTaxes();
    this.loadProducts();
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
      },
      error: () => {
        this.productErrorMessages = ['No se pudieron cargar las familias.'];
      },
    });
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
      },
      error: () => {
        this.productErrorMessages = ['No se pudieron cargar los impuestos.'];
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