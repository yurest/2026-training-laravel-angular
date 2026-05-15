import { Injectable, Injector } from '@angular/core';
import { Observable } from 'rxjs';
import { ApiResponse, BaseApiService } from '../../shared/data-access/base-api.service';

@Injectable({
  providedIn: 'root',
})
export class SaleService extends BaseApiService {
  private endpoint = '/sales';

  constructor(protected override injector: Injector) {
    super(injector);
  }

  getSales(params: any = null): Observable<ApiResponse> {
    return this.httpCall(this.endpoint, params, 'get');
  }

  getSale(id: string): Observable<ApiResponse> {
    return this.httpCall(`${this.endpoint}/${id}`, null, 'get');
  }

  getSaleLines(id: string): Observable<ApiResponse> {
    return this.httpCall(`${this.endpoint}/${id}/lines`, null, 'get');
  }
}