import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { BaseApiService } from '../core/services/api/base-api.service';

export interface TableItem {
  id: string;
  zone_id: string;
  name: string;
  created_at: string;
  updated_at: string;
}

interface CreateTablePayload {
  zone_id: string;
  name: string;
}

interface UpdateTablePayload {
  zone_id: string;
  name: string;
}

@Injectable({
  providedIn: 'root',
})
export class TableService extends BaseApiService {
  protected override readonly defaultErrorMessage = 'No se pudo completar la peticion de mesas.';

  public listTables(): Observable<TableItem[]> {
    return this.get<TableItem[]>('/admin/tables');
  }

  public createTable(payload: CreateTablePayload): Observable<TableItem> {
    return this.post<TableItem>('/admin/tables', payload);
  }

  public updateTable(id: string, payload: UpdateTablePayload): Observable<TableItem> {
    return this.put<TableItem>(`/admin/tables/${id}`, payload);
  }

  public deleteTable(id: string): Observable<void> {
    return this.delete<void>(`/admin/tables/${id}`);
  }
}
