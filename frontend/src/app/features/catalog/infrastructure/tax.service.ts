import { Injectable, Injector } from '@angular/core';
import { Observable } from 'rxjs';
import { BaseApiService, ApiResponse } from '../../../shared/data-access/base-api.service';
import { CreateTaxPayload, UpdateTaxPayload } from '../domain/tax.model';

@Injectable({
  providedIn: 'root',
})
export class TaxService extends BaseApiService {
  private endpoint = '/taxes';

  constructor(protected override injector: Injector) {
    super(injector);
  }

  getTaxes(): Observable<ApiResponse> {
    return this.httpCall(this.endpoint, null, 'get');
  }

  createTax(data: CreateTaxPayload): Observable<ApiResponse> {
    return this.httpCall(this.endpoint, data, 'post');
  }

  updateTax(id: string, data: UpdateTaxPayload): Observable<ApiResponse> {
    return this.httpCall(`${this.endpoint}/${id}`, data, 'put');
  }

  deleteTax(id: string): Observable<ApiResponse> {
    return this.httpCall(`${this.endpoint}/${id}`, null, 'delete');
  }
}