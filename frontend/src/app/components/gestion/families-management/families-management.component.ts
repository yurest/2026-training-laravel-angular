
import { Component, computed, input } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { GestionFamiliesFacade, FamilyRow, FamilyFormData } from '../../../pages/core/gestion/facades/gestion-families.facade';

@Component({
  selector: 'app-families-management',
  standalone: true,
  imports: [FormsModule],
  templateUrl: './families-management.component.html',
  styleUrls: ['./families-management.component.scss'],
})
export class FamiliesManagementComponent {
  public readonly facade = input.required<GestionFamiliesFacade>();

  public readonly families = computed(() => this.facade().families());
  public readonly formData = computed(() => this.facade().formData());
  public readonly selectedIndex = computed(() => this.facade().selectedIndex());
  public readonly isSaving = computed(() => this.facade().isSaving());

  isSelected(index: number): boolean {
    return this.selectedIndex() === index;
  }

  onSelect(index: number): void {
    this.facade().select(index);
  }

  onCreate(): void {
    this.facade().startCreate();
  }

  async onDelete(): Promise<void> {
    const result = await this.facade().deleteSelected();
    if (result.ok) {
      window.alert(result.message || 'Familia eliminada.');
    } else {
      window.alert(result.error || 'No se pudo eliminar la familia.');
    }
  }

  async onSubmit(): Promise<void> {
    const result = await this.facade().save();
    if (result.ok) {
      window.alert(result.message || 'Familia guardada.');
    } else {
      window.alert(result.error || 'No se pudo guardar la familia.');
    }
  }

  updateForm<K extends keyof FamilyFormData>(key: K, value: FamilyFormData[K]): void {
    this.facade().updateForm(key, value);
  }
}
