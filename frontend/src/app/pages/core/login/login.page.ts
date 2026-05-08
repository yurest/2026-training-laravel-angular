import { Component, inject } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { IonContent } from '@ionic/angular/standalone';
import { SearchPipe } from '../../../pipes';
import { LoginFacade, QuickUser } from './login.facade';

@Component({
  selector: 'app-login',
  templateUrl: './login.page.html',
  styleUrls: ['./login.page.scss'],
  imports: [ReactiveFormsModule, IonContent, SearchPipe],
  providers: [LoginFacade],
})
export class LoginPage {
  protected readonly facade = inject(LoginFacade);
  private readonly formBuilder = inject(FormBuilder);
  private readonly router = inject(Router);
  private readonly route = inject(ActivatedRoute);

  public readonly loginForm = this.formBuilder.nonNullable.group({
    email: ['', [Validators.required, Validators.email]],
    password: ['', [Validators.required]],
  });

  public pinValue = '';
  public selectedEmployee: QuickUser | null = null;
  public searchQuery = '';
  public showPinPanel = false;
  public showEmailForm = false;

  private returnUrl = '/app/caja';

  public ionViewWillEnter(): void {
    this.returnUrl = this.route.snapshot.queryParamMap.get('returnUrl') ?? '/app/caja';
    this.selectedEmployee = null;
    this.pinValue = '';
    this.showPinPanel = false;
    this.showEmailForm = false;
    this.facade.clearError();
    this.facade.refreshLinkedRestaurant();

    if (this.facade.linkedRestaurant()) {
      void this.facade.loadEmployees();
    } else {
      this.facade.skipEmployeesLoading();
    }
  }

  public onSearchChange(event: Event): void {
    this.searchQuery = (event.target as HTMLInputElement).value;
  }

  public selectEmployee(employee: QuickUser): void {
    this.selectedEmployee = employee;
    this.pinValue = '';
    this.facade.clearError();
    this.showPinPanel = true;
    this.showEmailForm = false;
  }

  public isSelectedEmployee(employee: QuickUser): boolean {
    return this.selectedEmployee?.userUuid === employee.userUuid;
  }

  public backToEmployeeList(): void {
    this.selectedEmployee = null;
    this.pinValue = '';
    this.facade.clearError();
    this.showPinPanel = false;
    this.showEmailForm = false;
  }

  public showClassicLogin(): void {
    this.selectedEmployee = null;
    this.pinValue = '';
    this.facade.clearError();
    this.showPinPanel = false;
    this.showEmailForm = true;
  }

  public isPinDotFilled(index: number): boolean {
    return index < this.pinValue.length;
  }

  public pinKey(value: string): void {
    if (this.facade.isSubmitting() || this.pinValue.length >= 4) {
      return;
    }

    this.pinValue += value;

    if (this.pinValue.length === 4) {
      void this.pinEnter();
    }
  }

  public pinDel(): void {
    if (this.facade.isSubmitting() || this.pinValue.length === 0) {
      return;
    }

    this.pinValue = this.pinValue.slice(0, -1);
  }

  public async pinEnter(): Promise<void> {
    if (this.facade.isSubmitting()) {
      return;
    }

    if (!this.selectedEmployee) {
      return;
    }

    if (this.pinValue.length !== 4) {
      return;
    }

    const success = await this.facade.loginWithPin(this.selectedEmployee.userUuid, this.pinValue);
    this.pinValue = '';

    if (success) {
      void this.router.navigateByUrl(this.returnUrl);
    }
  }

  public async submit(): Promise<void> {
    if (this.loginForm.invalid) {
      this.loginForm.markAllAsTouched();

      return;
    }

    const { email, password } = this.loginForm.getRawValue();
    const success = await this.facade.loginWithEmail(email, password);

    if (success) {
      void this.router.navigateByUrl(this.returnUrl);
    }
  }

  public goBack(): void {
    if (this.facade.linkedRestaurant() && (this.showPinPanel || this.showEmailForm)) {
      this.backToEmployeeList();
    } else {
      void this.router.navigateByUrl('/home');
    }
  }
}