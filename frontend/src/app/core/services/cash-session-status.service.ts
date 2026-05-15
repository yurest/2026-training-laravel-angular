import { Injectable, signal } from '@angular/core';

@Injectable({
  providedIn: 'root',
})
export class CashSessionStatusService {
  private readonly _isOpen = signal<boolean | null>(null);

  public readonly isOpen = this._isOpen.asReadonly();

  public setOpen(value: boolean): void {
    this._isOpen.set(value);
  }

  public clear(): void {
    this._isOpen.set(null);
  }
}
