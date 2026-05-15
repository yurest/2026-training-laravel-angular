import { Injectable } from '@angular/core';
import { AlertController } from '@ionic/angular';

@Injectable({
  providedIn: 'root',
})
export class AlertService {
  constructor(private alertController: AlertController) {}

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

  async confirmDelete(
    title: string,
    message: string,
    onConfirm: () => void,
  ): Promise<void> {
    const alert = await this.alertController.create({
      header: title,
      message,
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
          handler: onConfirm,
        },
      ],
    });

    await alert.present();
  }
}