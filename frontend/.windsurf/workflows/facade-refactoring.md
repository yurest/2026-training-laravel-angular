---
description: Refactorización de componentes usando el patrón Facade con Angular Signals
---

# Refactorización de Componentes usando Facade Pattern con Angular Signals

Este documento describe cómo refactorizar componentes Angular para usar el patrón Facade con Angular Signals, basado en la experiencia adquirida en el proyecto.

## Resumen de Refactorizaciones Completadas

### Módulo Gestion (completado)
- **GestionFamiliesFacade** — entidad Familias dentro de gestion.page
- **GestionTaxesFacade** — entidad Impuestos
- **GestionZonesFacade** — entidad Zonas y Mesas
- **GestionProductsFacade** — entidad Productos
- **GestionUsersFacade** — entidad Usuarios
- **GestionZReportsFacade** — listado Z-Reports
- **GestionRestaurantsFacade** — manejado por RestaurantListComponent/RestaurantDetailComponent

### Módulo Cash (completado)
- **CajaSessionFacade** — refactorización profunda de caja.page.ts (signals del facade integrados)
- **CajaPaymentFacade** — flujo de pago y métodos (integrado en caja.page.ts)
- **CajaSplitBillFacade** — ya integrado en CajaPaymentFacade

### Otros Módulos (completado)
- **ComandaFacade** — comanda.page.ts (~292→123 líneas)
- **MesasFacade** — mesas.page.ts (~570→447 líneas)
- **PedidosFacade** — pedidos.page.ts (~227→104 líneas)
- **LoginFacade** — login.page.ts (~257→149 líneas)
- **DeveloperDashboardFacade** — dashboard developer (~157 líneas)

### Pendientes
- **AppLayoutFacade** — requiere trabajo previo: separar dependencias circulares y simplificar estado del componente

## Pasos para Refactorizar un Componente con Facade

### 1. Analizar el Componente

Identificar:
- Propiedades de estado (BehaviorSubjects, variables simples)
- Servicios inyectados
- Métodos de negocio (llamadas a servicios)
- Métodos de UI (manejo de modales, navegación)

### 2. Crear el Facade

```typescript
import { Injectable, signal, computed, inject } from '@angular/core';
import { Observable, Subject, takeUntil } from 'rxjs';
import { YourService } from '../services/your.service';

@Injectable({
  providedIn: 'root'
})
export class YourFacade {
  private readonly service = inject(YourService);
  private readonly destroy$ = new Subject<void>();

  // Signals privados para el estado
  private readonly state = signal<'idle' | 'loading' | 'error'>('idle');
  private readonly items = signal<YourType[]>([]);
  private readonly error = signal<string | null>(null);

  // Signals readonly para consumo externo
  public readonly loading = computed(() => this.state() === 'loading');
  public readonly data = computed(() => this.items());
  public readonly errorMessage = computed(() => this.error());

  // Setters para modificar el estado
  public setState(value: 'idle' | 'loading' | 'error'): void {
    this.state.set(value);
  }

  public setItems(value: YourType[]): void {
    this.items.set(value);
  }

  public setError(value: string | null): void {
    this.error.set(value);
  }

  // Métodos de negocio
  public loadData(): Observable<YourType[]> {
    this.setState('loading');
    return this.service.getData().pipe(takeUntil(this.destroy$));
  }

  public createItem(item: YourType): Observable<YourType> {
    return this.service.create(item).pipe(takeUntil(this.destroy$));
  }

  public ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }
}
```

### 3. Integrar el Facade en el Componente

```typescript
import { Component, computed, inject, OnInit, OnDestroy } from '@angular/core';
import { YourFacade } from './facades/your.facade';

@Component({
  selector: 'app-your-page',
  templateUrl: './your.page.html',
  providers: [YourFacade], // Agregar el facade a providers
})
export class YourPage implements OnInit, OnDestroy {
  protected readonly facade = inject(YourFacade);

  // Reemplazar propiedades con computed signals del facade
  public readonly loading = computed(() => this.facade.loading());
  public readonly data = computed(() => this.facade.data());
  public readonly error = computed(() => this.facade.errorMessage());

  // Mantener solo propiedades de UI (modales, flags)
  public showModal = false;
  public selectedItem: YourType | null = null;

  ngOnInit(): void {
    this.facade.loadData().subscribe({
      next: (items) => this.facade.setItems(items),
      error: (err) => this.facade.setError(err.message),
    });
  }
}
```

### 4. Actualizar el Template HTML

Cambiar accesos a propiedades por llamadas a signals con paréntesis:

```html
<!-- Antes -->
@if (loading) { ... }
<div *ngFor="let item of data">...</div>

<!-- Después -->
@if (loading()) { ... }
<div *ngFor="let item of data()">...</div>
```

### 5. Corregir Errores Comunes

**Error: Cannot assign to X because it is a read-only property**
- Solución: Usar métodos setter del facade en lugar de asignación directa
- Ejemplo: `this.facade.setItems(items)` en lugar de `this.items = items`

**Error: This condition will always return true since this function is always defined**
- Solución: Llamar al signal con paréntesis
- Ejemplo: `if (loading())` en lugar de `if (loading)`

**Error: Property does not exist on type Signal<T>**
- Solución: Llamar al signal con paréntesis antes de acceder a propiedades
- Ejemplo: `this.session()?.status` en lugar de `this.session.status`

## Patrones y Convenciones

### Estructura del Facade

```
your-feature/
├── facades/
│   └── your-feature.facade.ts
├── services/
│   └── your.service.ts
└── pages/
    └── your-page/
        ├── your.page.ts
        └── your.page.html
```

### Nomenclatura

- **Nombre del facade**: `[Feature]Facade` (ej: CajaSessionFacade, ComandaFacade)
- **Nombre del archivo**: `[kebab-case].facade.ts` (ej: caja-session.facade.ts)
- **Signals privados**: `camelCase` con prefijo `private readonly`
- **Signals públicos**: `camelCase` con prefijo `public readonly`
- **Métodos setters**: `set[PropertyName]` (ej: setState, setItems)
- **Métodos de negocio**: `verb[Entity]` (ej: loadItems, createOrder)

### Signals vs BehaviorSubjects

**Usar Signals cuando:**
- El estado es simple y no requiere historial
- Solo necesitas el valor actual
- El componente solo necesita leer el estado

**Usar BehaviorSubjects cuando:**
- Necesitas el historial de cambios
- Múltiples componentes necesitan suscribirse al mismo estado
- Necesitas emitir eventos personalizados

## Errores a Evitar

1. **Dependencias circulares**: El facade no debe depender del componente
2. **Demasiada lógica en el componente**: Mover toda la lógica de negocio al facade
3. **Olvidar takeUntil**: Siempre usar `takeUntil(this.destroy$)` en subscriptions del facade
4. **No llamar signals con paréntesis**: Siempre usar `signal()` en el template
5. **Asignar a computed signals**: Usar métodos setter en lugar de asignación directa

## Ejemplo Completo: CajaSessionFacade

### Facade (caja-session.facade.ts)

```typescript
@Injectable({ providedIn: 'root' })
export class CajaSessionFacade {
  private readonly tpvService = inject(TpvService);
  private readonly destroy$ = new Subject<void>();

  private readonly state = signal<CajaState>('pre-apertura');
  private readonly activeSession = signal<TpvCashSession | null>(null);
  private readonly loading = signal<boolean>(false);

  public readonly sessionState = computed(() => this.state());
  public readonly currentSession = computed(() => this.activeSession());
  public readonly isLoading = computed(() => this.loading());

  public setState(value: CajaState): void {
    this.state.set(value);
  }

  public loadActiveSession(deviceId: string): Observable<TpvCashSession> {
    this.setLoading(true);
    return this.tpvService.getActiveCashSession(deviceId).pipe(takeUntil(this.destroy$));
  }

  // ... más métodos
}
```

### Componente (caja.page.ts)

```typescript
@Component({
  providers: [CajaSessionFacade],
})
export class CajaPage {
  protected readonly sessionFacade = inject(CajaSessionFacade);

  public readonly state = computed(() => this.sessionFacade.sessionState());
  public readonly activeSession = computed(() => this.sessionFacade.currentSession());
  public readonly loading = computed(() => this.sessionFacade.isLoading());

  public onOpenCash(): void {
    // Usar métodos del facade
  }
}
```

## Checklist de Refactorización

- [ ] Crear el facade en la carpeta `facades/`
- [ ] Mover servicios inyectados al facade
- [ ] Crear signals privados para el estado
- [ ] Crear computed signals públicos para consumo externo
- [ ] Crear métodos setters para modificar el estado
- [ ] Mover métodos de negocio al facade
- [ ] Agregar el facade a providers del componente
- [ ] Reemplazar propiedades del componente con computed signals
- [ ] Actualizar el template para usar signals con paréntesis
- [ ] Corregir errores de compilación
- [ ] Verificar que la funcionalidad siga funcionando
- [ ] Ejecutar `npx tsc --noEmit` para verificar que no hay errores

## Referencias

- Documentación de Angular Signals: https://angular.dev/guide/signals
- Patrón Facade: https://refactoring.guru/design-patterns/facade
- Convenciones del proyecto: Ver AGENTS.md
