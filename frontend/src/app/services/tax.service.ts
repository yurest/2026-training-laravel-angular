import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { BaseApiService } from '../core/services/api/base-api.service';

export interface TaxItem {
  id: string;
  name: string;
  percentage: number;
  created_at: string;
  updated_at: string;
}

interface CreateTaxPayload {
  name: string;
  percentage: number;
}

interface UpdateTaxPayload {
  name: string;
  percentage: number;
}

@Injectable({
  providedIn: 'root',
})
export class TaxService extends BaseApiService {
  protected override readonly defaultErrorMessage = 'No se pudo completar la peticion de impuestos.';

  public listTaxes(): Observable<TaxItem[]> {
    return this.get<TaxItem[]>('/admin/taxes');
  }

  public createTax(payload: CreateTaxPayload): Observable<TaxItem> {
    return this.post<TaxItem>('/admin/taxes', payload);
  }

  public updateTax(id: string, payload: UpdateTaxPayload): Observable<TaxItem> {
    return this.put<TaxItem>(`/admin/taxes/${id}`, payload);
  }

  public deleteTax(id: string): Observable<void> {
    return this.delete<void>(`/admin/taxes/${id}`);
  }
}
