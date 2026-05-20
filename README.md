# TPV Restaurant Management System

Sistema TPV (Terminal Punto de Venta) desarrollado con Laravel + Angular + Ionic para la gestión de restaurantes.

El proyecto incluye una zona de administración (Backoffice) y un entorno de venta táctil orientado a tablets y uso diario en hostelería.

---

## Índice

- [Tecnologías](#tecnologías)
- [Funcionalidades](#funcionalidades)
  - [Backoffice](#backoffice)
  - [TPV](#tpv)
- [Arquitectura](#arquitectura)
  - [Backend](#backend)
  - [Frontend](#frontend)
  - [DbGate](#dbgate)
- [Instalación](#instalación)
  - [Prerrequisitos](#prerrequisitos)
  - [Cómo empezar](#cómo-empezar)
  - [Migraciones y seeders](#migraciones-y-seeders)
- [Servicios Docker](#servicios-docker)
- [Estado actual del proyecto](#estado-actual-del-proyecto)
- [Futuras mejoras](#futuras-mejoras)
- [Buenas prácticas](#buenas-prácticas)
- [Estilo de código](#estilo-de-código)
- [Proyecto académico](#proyecto-académico)

---

## Tecnologías

### Backend

- Laravel 12
- PHP 8
- MySQL
- Laravel Sanctum
- Docker
- Arquitectura DDD + Hexagonal

### Frontend

- Angular 20
- Ionic
- TypeScript
- Standalone Components

---

## Funcionalidades

### Backoffice

- Login mediante autenticación por token
- CRUD de productos
- CRUD de familias
- CRUD de impuestos
- CRUD de zonas
- CRUD de mesas
- Activación y desactivación de productos
- Gestión de imágenes de productos

### TPV

- Visualización de mesas por zonas
- Control visual de mesas libres y ocupadas
- Apertura de pedidos
- Gestión de productos agrupados por familias
- Añadir, eliminar y modificar líneas de pedido
- Envío de productos a cocina
- Control de líneas enviadas
- Precuenta imprimible
- Cobro de pedidos
- Pago en efectivo
- Pago con tarjeta
- Pago mixto
- Cálculo automático de cambio
- Ticket final imprimible
- Liberación automática de mesas al cerrar venta

---

## Arquitectura

### Backend

El backend sigue un enfoque **DDD + Hexagonal**, con cada dominio encapsulado bajo su propio namespace.

```text
App/
└── Product/
    ├── Domain/
    │   ├── Entity/
    │   ├── Interfaces/
    │   └── ValueObject/
    ├── Application/
    └── Infrastructure/
```

| Carpeta | Descripción |
|---|---|
| Domain | Lógica de negocio pura |
| Interfaces | Contratos del dominio |
| Application | Casos de uso y handlers |
| Infrastructure | Persistencia y entrypoints HTTP |

---

### Frontend

Proyecto Angular + Ionic basado en standalone components y organización por features.

```text
frontend/src/app/
├── core/
├── shared/
├── features/
│   ├── identity/
│   ├── floor/
│   ├── orders/
│   ├── catalog/
│   ├── settings/
│   └── sales/
```

| Carpeta | Descripción |
|---|---|
| core | Configuración global y layouts |
| shared | Componentes reutilizables |
| features | Funcionalidad separada por dominio |

El interceptor HTTP añade automáticamente:
- URL base de la API
- Header `Accept`
- Header `Accept-Language`
- Token Bearer de autenticación

---

### DbGate

Interfaz web para explorar y consultar la base MySQL.

La conexión **Training MySQL** queda preconfigurada y apunta a la base `training` del servicio `db`.

---

## Instalación

### Prerrequisitos

Es necesario tener instalado:

- Docker
- Docker Compose
- Make
- Git

---

### Cómo empezar

#### 1. Clonar el repositorio

```bash
git clone <repo-url>
cd 2026-training-laravel-angular
```

#### 2. Configurar entorno backend

```bash
cp backend/.env.example backend/.env
```

#### 3. Levantar contenedores Docker

```bash
make start
```

#### 4. Instalar dependencias backend

```bash
make install
docker compose run --rm api php artisan key:generate
```

Si el contenedor `api` no quedó iniciado:

```bash
make start
```

#### 5. Frontend

El frontend se levanta automáticamente con:

```bash
make start
```

Para desarrollo con live reload:

```bash
make serve-frontend
```

---

### Migraciones y seeders

```bash
docker compose exec api php artisan migrate:fresh --seed
```

---

## Servicios Docker

| Servicio | URL |
|---|---|
| API Laravel | http://localhost:8000 |
| Frontend Angular | http://localhost:4200 |
| DbGate | http://localhost:9051 |

---

## Estado actual del proyecto

Actualmente el proyecto incluye:

- Backend funcional con arquitectura DDD
- CRUDs principales implementados
- Front de venta funcional
- Sistema de pedidos
- Tickets y precuentas
- Cobro de ventas
- Integración completa frontend/backend

---

## Futuras mejoras

- Informes de ventas
- Integración con impresoras térmicas
- Autenticación por PIN
- División de cuentas
- Cierre de caja
- Traslado de mesas
- WebSockets en tiempo real

---

## Buenas prácticas

- Programar contra interfaces
- Evitar lógica de negocio en Controllers o Models
- Mantener dominios autocontenidos
- Escribir tests desacoplados de infraestructura

---

## Estilo de código

### Backend

- PSR-12
- Symfony Coding Standards

### Frontend

- Angular Style Guide

### Convenciones

- Una clase por archivo
- camelCase para variables y métodos
- PascalCase para clases
- Imports explícitos
- Arrays multilínea con coma final
- Uso de llaves en bloques condicionales

---

Antes de subir cambios:

```bash
make test
make lint
```

---

## Proyecto de prácticas

Proyecto desarrollado durante las prácticas de Desarrollo de Aplicaciones Multiplataforma (DAM), utilizando Laravel + Angular + Ionic para la construcción de un sistema TPV orientado a restauración.

El objetivo del proyecto es aplicar arquitecturas y flujos de trabajo utilizados en entornos profesionales.

Actualmente el proyecto continúa en desarrollo y ampliación de funcionalidades.