import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { IonContent, IonButton } from '@ionic/angular/standalone';
import { ProductsSettingsComponent } from '../../../catalog/ui/admin/products-settings/products-settings.component';
import { FamiliesSettingsComponent } from '../../../catalog/ui/admin/families-settings/families-settings.component';
import { TaxesSettingsComponent } from '../../../catalog/ui/admin/taxes-settings/taxes-settings.component';
import { ZonesSettingsComponent } from '../../../floor/ui/admin/zones-settings/zones-settings.component';
import { TablesSettingsComponent } from '../../../floor/ui/admin/tables-settings/tables-settings.component';
import { UsersSettingsComponent } from '../../../identity/ui/admin/users-settings/users-settings.component';

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
    IonContent,
    IonButton,
    UsersSettingsComponent,
    ProductsSettingsComponent,
    FamiliesSettingsComponent,
    TaxesSettingsComponent,
    ZonesSettingsComponent,
    TablesSettingsComponent,
  ],
})
export class SettingsComponent {
  selectedSection: SettingsSection = 'users';

  selectSection(section: SettingsSection): void {
    this.selectedSection = section;
  }

  isSectionActive(section: SettingsSection): boolean {
    return this.selectedSection === section;
  }
}
