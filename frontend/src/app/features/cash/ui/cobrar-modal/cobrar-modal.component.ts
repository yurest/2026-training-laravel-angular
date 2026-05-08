import { Component, Input, Output, EventEmitter, OnChanges, SimpleChanges } from '@angular/core';

import { FormsModule } from '@angular/forms';
import { CardComponent } from '../../../../shared/components/card/card.component';
import { BtnComponent } from '../../../../shared/components/btn/btn.component';
import { ToggleComponent } from '../../../../shared/components/toggle/toggle.component';
import { NumpadComponent } from '../../../../shared/components/numpad/numpad.component';
import { AmountDisplayComponent } from '../../../../shared/components/amount-display/amount-display.component';
import { DinersStatusComponent } from '../../../../shared/components/diners-status/diners-status.component';
import { PaymentMethod } from '../../../../core/enums/payment-method.enum';

export interface OrderLine {
  id?: string;
  name: string;
  price: number;
  diner?: number | null;
}

@Component({
  selector: 'app-cobrar-modal',
  templateUrl: './cobrar-modal.component.html',
  styleUrls: ['./cobrar-modal.component.scss'],
  imports: [FormsModule, CardComponent, BtnComponent, ToggleComponent, NumpadComponent, AmountDisplayComponent, DinersStatusComponent],
  standalone: true,
})
export class CobrarModalComponent implements OnChanges {
  @Input() isOpen = false;
  @Input() total = 0;
  @Input() tableLabel = '';
  @Input() lines: OrderLine[] = [];
  @Input() isPartialPayment = false;
  @Input() isProcessing = false;
  @Input() diners = 0;
  @Input() paidDiners: number[] = [];
  @Output() closeModal = new EventEmitter<void>();
  @Output() confirmPayment = new EventEmitter<{ method: PaymentMethod; amount: number; tip?: number; isManualPartial?: boolean }>();
  @Output() splitBill = new EventEmitter<void>();

  public Math = Math;
  public method: PaymentMethod = PaymentMethod.CASH;
  public inputAmount = 0;
  public tip = 0;
  public showTip = false;
  public showFiscal = false;

  public get effectiveAmount(): number {
    return Math.min(this.inputAmount, this.total);
  }

  public get change(): number {
    if (this.method !== PaymentMethod.CASH) return 0;
    return this.inputAmount - this.effectiveAmount;
  }

  public get remainingTotal(): number {
    if (this.diners <= 0) return this.total;
    const perDiner = Math.floor(this.total / this.diners);
    return this.total - (this.paidDiners.length * perDiner);
  }

  public ngOnChanges(changes: SimpleChanges): void {
    const justOpened = changes['isOpen'] && this.isOpen && !changes['isOpen'].previousValue;
    const totalChangedWhileOpen = changes['total'] && this.isOpen;
    if (justOpened || totalChangedWhileOpen) {
      this.inputAmount = this.total;
    }
  }

  private methodLabels: { [key: string]: string } = {
    [PaymentMethod.CASH]: 'Efectivo',
    [PaymentMethod.CARD]: 'Tarjeta',
    [PaymentMethod.BIZUM]: 'Bizum',
    [PaymentMethod.MIXED]: 'Mixto',
    [PaymentMethod.INVITATION]: 'Invitación',
  };

  public get methods(): Array<{ value: PaymentMethod; label: string; icon: string }> {
    return [
      { value: PaymentMethod.CASH, label: 'Efectivo', icon: 'cash' },
      { value: PaymentMethod.CARD, label: 'Tarjeta', icon: 'card' },
      { value: PaymentMethod.BIZUM, label: 'Bizum', icon: 'phone' },
      { value: PaymentMethod.MIXED, label: 'Mixto', icon: 'mixed' },
      { value: PaymentMethod.INVITATION, label: 'Invitación', icon: 'gift' },
    ];
  }



  public onClose(): void {
    this.closeModal.emit();
    this.resetForm();
  }

  public onConfirm(): void {
    const tip = this.showTip ? this.tip : 0;
    const amountToPay = this.effectiveAmount;

    if (amountToPay <= 0) {
      alert('El importe a cobrar debe ser mayor a 0.');
      return;
    }

    if (this.method === PaymentMethod.CASH) {
      if (this.inputAmount > this.total * 2 && this.total > 0) {
        alert('El importe introducido parece demasiado alto. Por favor, verifíquelo.');
        return;
      }
    }

    if (tip > amountToPay) {
      alert('La propina no puede superar el importe a cobrar.');
      return;
    }

    const isManualPartial = amountToPay < this.total;

    this.confirmPayment.emit({
      method: this.method,
      amount: amountToPay + tip,
      tip: tip > 0 ? tip : undefined,
      isManualPartial: isManualPartial,
    });
    this.resetForm();
  }

  public onSplitBill(): void {
    this.splitBill.emit();
  }

  public onCashGivenChange(value: number): void {
    this.inputAmount = value;
  }

  public setQuickAmount(amount: number): void {
    this.inputAmount = amount;
  }

  public formatCents(cents: number): string {
    return (cents / 100).toFixed(2);
  }

  public abs(value: number): number {
    return Math.abs(value);
  }

  private resetForm(): void {
    this.method = PaymentMethod.CASH;
    this.inputAmount = 0;
    this.tip = 0;
    this.showTip = false;
    this.showFiscal = false;
  }
}
