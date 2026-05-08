import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { BaseApiService } from '../core/services/api/base-api.service';

export interface FamilyItem {
  id: string;
  name: string;
  active: boolean;
  created_at: string;
  updated_at: string;
}

interface CreateFamilyPayload {
  name: string;
}

interface UpdateFamilyPayload {
  name: string;
}

@Injectable({
  providedIn: 'root',
})
export class FamilyService extends BaseApiService {
  protected override readonly defaultErrorMessage = 'No se pudo completar la peticion de familias.';

  public listFamilies(): Observable<FamilyItem[]> {
    return this.get<FamilyItem[]>('/admin/families');
  }

  public createFamily(payload: CreateFamilyPayload): Observable<FamilyItem> {
    return this.post<FamilyItem>('/admin/families', payload);
  }

  public updateFamily(id: string, payload: UpdateFamilyPayload): Observable<FamilyItem> {
    return this.put<FamilyItem>(`/admin/families/${id}`, payload);
  }

  public activateFamily(id: string): Observable<FamilyItem> {
    return this.patch<FamilyItem>(`/admin/families/${id}/activate`);
  }

  public deactivateFamily(id: string): Observable<FamilyItem> {
    return this.patch<FamilyItem>(`/admin/families/${id}/deactivate`);
  }

  public deleteFamily(id: string): Observable<void> {
    return this.delete<void>(`/admin/families/${id}`);
  }
}
