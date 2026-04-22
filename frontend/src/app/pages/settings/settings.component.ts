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
import { AuthService } from '../../services/auth/auth.service';

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
  users: User[] = [];
  loading = false;
  errorMessage = '';

  createForm = {
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'operator',
    pin: '',
    image_src: '',
  };

  constructor(
    private userService: UserService,
    private authService: AuthService,
    private alertController: AlertController,
  ) {}

  ngOnInit(): void {
    this.loadUsers();
  }

  loadUsers(): void {
    this.loading = true;
    this.errorMessage = '';

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

        this.loading = false;
      },
      error: (error) => {
        console.log('ERROR loading users', error);
        this.errorMessage = 'No se pudieron cargar los usuarios.';
        this.loading = false;
      },
    });
  }

  createUser(): void {
    const loggedUser = this.authService.getUser();
    const restaurantId = loggedUser?.restaurant_id;

    this.errorMessage = '';

    if (!restaurantId) {
      this.errorMessage = 'No se ha encontrado el restaurant_id.';
      return;
    }

    if (
      !this.createForm.name.trim() ||
      !this.createForm.email.trim() ||
      !this.createForm.password ||
      !this.createForm.password_confirmation
    ) {
      this.errorMessage = 'Completa los campos obligatorios.';
      return;
    }

    if (this.createForm.password !== this.createForm.password_confirmation) {
      this.errorMessage = 'Las contraseñas no coinciden.';
      return;
    }

    if (this.createForm.password.length < 8) {
      this.errorMessage = 'La contraseña debe tener mínimo 8 caracteres';
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
        console.log(error);
        this.errorMessage = 'No se pudo crear el usuario.';
      },
    });
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
        this.errorMessage = 'No se pudo eliminar el usuario.';
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