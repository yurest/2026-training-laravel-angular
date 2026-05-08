import { inject, Injectable, Signal, signal } from '@angular/core';
import { firstValueFrom } from 'rxjs';
import { TpvService, TpvCashSessionListItem } from '../../../../features/cash/services/tpv.service';

export interface ZReportRow {
  id: string;
  zNum: number;
  date: string;
  opened: string;
  closed: string;
  tickets: number;
  diners: number;
  gross: number;
  discounts: number;
  invitations: number;
  invValue: number;
  cancellations: number;
  net: number;
  initial: number;
  movIn: number;
  movOut: number;
  expected: number;
  counted: number;
  diff: number;
  diffReason?: string;
}

@Injectable()
export class GestionZReportsFacade {
  private readonly tpvService = inject(TpvService);

  private readonly _reports = signal<ZReportRow[]>([]);
  private readonly _isLoading = signal<boolean>(false);

  public readonly reports: Signal<ZReportRow[]> = this._reports.asReadonly();
  public readonly isLoading: Signal<boolean> = this._isLoading.asReadonly();

  public async load(): Promise<void> {
    this._isLoading.set(true);

    try {
      const response = await firstValueFrom(this.tpvService.listCashSessions());
      const rows: ZReportRow[] = (response as { sessions: TpvCashSessionListItem[] }).sessions.map((session): ZReportRow => ({
        id: session.uuid,
        zNum: session.z_report_number || 0,
        date: session.closed_at || session.opened_at || '',
        opened: session.opened_at || '',
        closed: session.closed_at || '',
        tickets: session.tickets || 0,
        diners: session.diners || 0,
        gross: session.gross || 0,
        discounts: session.discounts || 0,
        invitations: session.invitations || 0,
        invValue: session.inv_value || 0,
        cancellations: session.cancellations || 0,
        net: session.net || session.final_amount_cents || session.expected_amount_cents || 0,
        initial: session.initial_amount_cents,
        movIn: session.mov_in || 0,
        movOut: session.mov_out || 0,
        expected: session.expected_amount_cents || 0,
        counted: session.final_amount_cents || 0,
        diff: session.discrepancy_cents || 0,
        diffReason: session.discrepancy_reason || undefined,
      }));

      this._reports.set(rows);
    } finally {
      this._isLoading.set(false);
    }
  }

  public clear(): void {
    this._reports.set([]);
  }
}
