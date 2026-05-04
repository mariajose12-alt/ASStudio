# AS-Studio — Estructura del Proyecto

> **Stack:** Laravel 11 · Blade · PostgreSQL · S3 · Gmail SMTP  
> **Arquitectura:** Cliente-Servidor en 3 capas — Controller → Service → Repository → Model  
> **Equipo:** 3 personas · Metodología Scrum · GitHub + Jira

---

## Regla de oro de la arquitectura

```
HTTP Request
    ↓
Controller      → solo recibe y responde HTTP, sin lógica de negocio
    ↓
DTO             → transporta datos entre capas de forma segura
    ↓
Service         → toda la lógica de negocio vive aquí
    ↓
Repository      → solo consultas y escrituras a la BD
    ↓
Model           → define tablas, relaciones y atributos
    ↓
PostgreSQL
```

---

## Árbol de carpetas

```
asstudio/
│
├── app/
│   ├── DTOs/                          # Data Transfer Objects
│   ├── Events/                        # Eventos del sistema
│   ├── Factories/                     # Fábricas de objetos
│   ├── Http/
│   │   ├── Controllers/               # Controllers HTTP
│   │   ├── Middleware/                # Middlewares (ej: rol)
│   │   └── Requests/                  # Form Requests (validaciones)
│   ├── Listeners/                     # Listeners de eventos
│   ├── Mail/                          # Clases Mailable (emails)
│   ├── Models/                        # Modelos Eloquent
│   ├── Providers/                     # Service Providers
│   ├── Repositories/
│   │   ├── Contracts/                 # Interfaces de repositorios
│   │   └── *.php                      # Implementaciones
│   ├── Services/                      # Servicios de negocio
│   ├── Strategies/                    # Patrón Strategy
│   └── ValueObjects/                  # Value Objects del dominio
│
├── database/
│   ├── migrations/                    # Migraciones de BD
│   └── seeders/                       # Seeders de datos
│
├── resources/
│   └── views/
│       ├── admin/                     # Vistas del administrador
│       ├── cliente/                   # Vistas del cliente
│       ├── emails/                    # Plantillas de emails
│       ├── fotografo/                 # Vistas del fotógrafo
│       ├── layouts/                   # Layouts base
│       └── reservas/                  # Flujo de reservas (pasos)
│
├── routes/
│   ├── web.php                        # Todas las rutas web
│   └── auth.php                       # Rutas de autenticación
│
└── storage/
    └── logs/
        └── laravel.log                # Log del sistema
```

---

## app/DTOs/ — Data Transfer Objects

Transportan datos entre Controller → Service → Repository.  
**Nunca** contienen lógica de negocio.

| Archivo | Descripción | Estado |
|---|---|---|
| `ReservaCreateDTO.php` | Datos para crear una reserva | ✅ Hecho |
| `EmpleadoCreateDTO.php` | Datos para crear/editar empleado | ✅ Hecho |
| `PagoCreateDTO.php` | Datos para registrar un pago | ❌ Pendiente |
| `GaleriaSeleccionDTO.php` | Fotos seleccionadas por el cliente | ❌ Pendiente |
| `NominaResumenDTO.php` | Datos del cálculo de nómina | ❌ Pendiente |

**Patrón de uso:**
```php
// Se construye desde el Request en el Controller
$dto = ReservaCreateDTO::fromRequest($request);

// El Service lo recibe y usa
public function crearReserva(ReservaCreateDTO $dto): Reserva
```

---

## app/Events/ — Eventos del sistema

Se disparan desde los Services cuando ocurre algo importante.  
**No hacen nada por sí solos** — solo notifican que algo ocurrió.

| Archivo | Cuándo se dispara | Estado |
|---|---|---|
| `ReservaCreada.php` | Cliente envía solicitud de reserva | ✅ Hecho |
| `ReservaAprobada.php` | Fotógrafo aprueba una reserva | ❌ Pendiente |
| `ReservaRechazada.php` | Fotógrafo rechaza una reserva | ❌ Pendiente |
| `PagoConfirmado.php` | Cliente realiza el pago | ❌ Pendiente |
| `GaleriaDisponible.php` | Fotógrafo sube las fotos | ❌ Pendiente |
| `SeleccionConfirmada.php` | Cliente confirma selección de fotos | ❌ Pendiente |
| `NominaGenerada.php` | Admin procesa la nómina mensual | ❌ Pendiente |

**Patrón de uso:**
```php
// Se dispara en el Service, una sola línea
ReservaCreada::dispatch($reserva);
```

---

## app/Listeners/ — Listeners de eventos

Escuchan los eventos y ejecutan acciones (enviar emails, logs, etc).  
Se registran en `app/Providers/EventServiceProvider.php`.

| Archivo | Escucha a | Acción | Estado |
|---|---|---|---|
| `EnviarNotificacionNuevaReserva.php` | `ReservaCreada` | Email al fotógrafo | ✅ Hecho |
| `EnviarNotificacionReservaAprobada.php` | `ReservaAprobada` | Email al cliente | ❌ Pendiente |
| `EnviarNotificacionReservaRechazada.php` | `ReservaRechazada` | Email al cliente | ❌ Pendiente |
| `EnviarNotificacionPagoConfirmado.php` | `PagoConfirmado` | Email al cliente | ❌ Pendiente |
| `EnviarNotificacionGaleriaLista.php` | `GaleriaDisponible` | Email al cliente | ❌ Pendiente |
| `EnviarNotificacionSeleccion.php` | `SeleccionConfirmada` | Email al fotógrafo | ❌ Pendiente |

**Patrón de uso:**
```php
// app/Providers/EventServiceProvider.php
protected $listen = [
    ReservaCreada::class => [
        EnviarNotificacionNuevaReserva::class,
    ],
];
```

---

## app/Mail/ — Clases Mailable

Cada email del sistema tiene su propio Mailable.  
La vista HTML del email vive en `resources/views/emails/`.

| Archivo | Destinatario | Vista | Estado |
|---|---|---|---|
| `NuevaReservaFotografo.php` | Fotógrafo | `emails/reserva-nueva-fotografo.blade.php` | ✅ Hecho |
| `ReservaAprobadaCliente.php` | Cliente | `emails/reserva-aprobada-cliente.blade.php` | ❌ Pendiente |
| `ReservaRechazadaCliente.php` | Cliente | `emails/reserva-rechazada-cliente.blade.php` | ❌ Pendiente |
| `PagoConfirmadoCliente.php` | Cliente | `emails/pago-confirmado-cliente.blade.php` | ❌ Pendiente |
| `GaleriaDisponibleCliente.php` | Cliente | `emails/galeria-disponible-cliente.blade.php` | ❌ Pendiente |
| `SeleccionConfirmadaFotografo.php` | Fotógrafo | `emails/seleccion-confirmada-fotografo.blade.php` | ❌ Pendiente |

---

## app/Http/Controllers/ — Controllers

Solo reciben HTTP y devuelven respuestas. **Máximo 5 líneas de lógica.**  
Toda lógica real va en el Service correspondiente.

| Archivo | Rol | Módulo | Estado |
|---|---|---|---|
| `ReservaController.php` | Cliente | Flujo reserva (4 pasos) | ✅ Hecho |
| `ClienteReservaController.php` | Cliente | Ver historial de reservas | ✅ Hecho |
| `ClienteController.php` | Cliente | Dashboard y perfil | ✅ Hecho |
| `AdminController.php` | Admin | Dashboard y gestión de reservas | ✅ Hecho |
| `EmpleadoController.php` | Admin | CRUD empleados | ✅ Hecho |
| `PaqueteController.php` | Admin | CRUD paquetes | ✅ Hecho |
| `CatalogoController.php` | Admin | CRUD catálogos | ✅ Hecho |
| `FotografoController.php` | Fotógrafo | Dashboard y calendario | ✅ Hecho |
| `DisponibilidadController.php` | Público | API fechas ocupadas | ✅ Hecho |
| `FotografoReservaController.php` | Fotógrafo | Aprobar / Rechazar reservas | ❌ Pendiente |
| `PagoController.php` | Cliente | Flujo de pago | ❌ Pendiente |
| `FotografiaController.php` | Fotógrafo | Subir fotografías | ❌ Pendiente |
| `GaleriaController.php` | Cliente | Seleccionar fotografías | ❌ Pendiente |
| `NominaController.php` | Admin | Gestión de nómina mensual | ❌ Pendiente |

---

## app/Http/Requests/ — Form Requests

Centralizan las validaciones fuera de los Controllers.

| Archivo | Usado en | Estado |
|---|---|---|
| `StorePaqueteRequest.php` | `PaqueteController::store` | ❌ Pendiente |
| `UpdatePaqueteRequest.php` | `PaqueteController::update` | ❌ Pendiente |
| `StoreCatalogoRequest.php` | `CatalogoController::store` | ❌ Pendiente |
| `UpdateCatalogoRequest.php` | `CatalogoController::update` | ❌ Pendiente |
| `StoreEmpleadoRequest.php` | `EmpleadoController::store` | ❌ Pendiente |
| `UpdateEmpleadoRequest.php` | `EmpleadoController::update` | ❌ Pendiente |
| `StorePagoRequest.php` | `PagoController::store` | ❌ Pendiente |

---

## app/Services/ — Servicios de negocio

Toda la lógica de negocio vive aquí. Los Controllers llaman a los Services.  
Los Services llaman a los Repositories. **Nunca** al Model directamente.

| Archivo | Responsabilidad | Estado |
|---|---|---|
| `ReservaService.php` | Crear reserva, asignar fotógrafo, validar disponibilidad | ✅ Hecho |
| `EmpleadoService.php` | Crear / editar / eliminar empleados y fotógrafos | ✅ Hecho |
| `AdminService.php` | Métricas del dashboard, cambiar estado de reserva | ✅ Hecho |
| `FotografoService.php` | Métricas dashboard fotógrafo, eventos calendario | ✅ Hecho |
| `ClienteService.php` | Métricas dashboard cliente, sesiones por mes | ✅ Hecho |
| `DisponibilidadService.php` | Buscar fotógrafos disponibles en fecha/hora | ❌ Pendiente |
| `PagoService.php` | Registrar pago, generar comprobante, cambiar estado | ❌ Pendiente |
| `GaleriaService.php` | Subir fotos a S3, confirmar selección del cliente | ❌ Pendiente |
| `NominaService.php` | Calcular nómina mensual, aplicar comisiones | ❌ Pendiente |

---

## app/Repositories/ — Repositorios

Solo hablan con la base de datos. Sin lógica de negocio.  
Los Services los usan a través de sus interfaces.

### app/Repositories/Contracts/ — Interfaces

| Archivo | Descripción | Estado |
|---|---|---|
| `ReservaRepositoryInterface.php` | Contrato del repositorio de reservas | ✅ Hecho |
| `PagoRepositoryInterface.php` | Contrato del repositorio de pagos | ❌ Pendiente |
| `GaleriaRepositoryInterface.php` | Contrato del repositorio de galería | ❌ Pendiente |
| `NominaRepositoryInterface.php` | Contrato del repositorio de nómina | ❌ Pendiente |

### Implementaciones

| Archivo | Métodos principales | Estado |
|---|---|---|
| `ReservaRepository.php` | `crear()`, `porCliente()`, `hayDisponibilidad()` | ✅ Hecho |
| `PagoRepository.php` | `crear()`, `porReserva()` | ❌ Pendiente |
| `GaleriaRepository.php` | `guardarFotos()`, `seleccionPorSesion()` | ❌ Pendiente |
| `NominaRepository.php` | `crear()`, `porPeriodo()`, `porFotografo()` | ❌ Pendiente |

**Binding en AppServiceProvider:**
```php
// app/Providers/AppServiceProvider.php
$this->app->bind(
    ReservaRepositoryInterface::class,
    ReservaRepository::class,
);
```

---

## app/Models/ — Modelos Eloquent

Cada modelo representa una tabla. Solo definen relaciones, atributos y scopes.  
**No contienen lógica de negocio.**

| Modelo | Tabla | Relaciones principales | Estado |
|---|---|---|---|
| `Persona.php` | `personas` | hasOne Usuario | ✅ Hecho |
| `Usuario.php` | `usuarios` | belongsTo Persona, hasOne Cliente/Empleado | ✅ Hecho |
| `Cliente.php` | `clientes` | belongsTo Usuario, hasMany Reserva | ✅ Hecho |
| `Empleado.php` | `empleados` | belongsTo Usuario, hasOne Fotografo/Admin | ✅ Hecho |
| `Fotografo.php` | `fotografos` | belongsTo Empleado, hasMany Agenda | ✅ Hecho |
| `Administrador.php` | `administradores` | belongsTo Empleado | ✅ Hecho |
| `Catalogo.php` | `catalogos` | belongsToMany PaqueteFotografico | ✅ Hecho |
| `PaqueteFotografico.php` | `paquetes_fotograficos` | belongsToMany Catalogo | ✅ Hecho |
| `Reserva.php` | `reservas` | belongsTo Cliente, Fotografo, Paquete | ✅ Hecho |
| `Agenda.php` | `agendas` | belongsTo Fotografo | ✅ Hecho |
| `Sesion.php` | `sesiones` | belongsTo Reserva, belongsToMany Fotografo | ⚠️ Parcial |
| `ParticipacionSesion.php` | `participaciones_sesion` | pivot Fotografo-Sesion | ⚠️ Parcial |
| `Fotografia.php` | `fotografias` | belongsTo Sesion | ❌ Pendiente |
| `Pago.php` | `pagos` | belongsTo Reserva | ❌ Pendiente |
| `Comprobante.php` | `comprobantes` | belongsTo Pago | ❌ Pendiente |
| `Reembolso.php` | `reembolsos` | belongsTo Pago | ❌ Pendiente |
| `Nomina.php` | `nominas` | hasMany DetalleNomina | ❌ Pendiente |
| `DetalleNomina.php` | `detalle_nomina` | belongsTo Nomina, Fotografo | ❌ Pendiente |
| `Notificacion.php` | `notificaciones` | belongsTo Usuario | ❌ Pendiente |

---

## app/Strategies/ — Patrón Strategy

Para lógica que varía según contexto (métodos de pago, comisiones).

| Archivo | Descripción | Estado |
|---|---|---|
| `Comisiones/ComisionPrincipalStrategy.php` | Comisión fotógrafo principal | ❌ Pendiente |
| `Comisiones/ComisionAsistenteStrategy.php` | Comisión fotógrafo asistente | ❌ Pendiente |
| `Pagos/PagoTarjetaStrategy.php` | Lógica pago con tarjeta | ❌ Pendiente |
| `Pagos/PagoTransferenciaStrategy.php` | Lógica pago con transferencia | ❌ Pendiente |

---

## app/ValueObjects/ — Value Objects

Objetos inmutables que representan conceptos del dominio con sus propias reglas.

| Archivo | Descripción | Estado |
|---|---|---|
| `EstadoReserva.php` | Estados válidos de una reserva | ❌ Pendiente |
| `Monto.php` | Monto monetario (nunca negativo) | ❌ Pendiente |
| `PorcentajeComision.php` | Porcentaje entre 0 y 100 | ❌ Pendiente |
| `TipoSesion.php` | ESTUDIO o EXTERIOR | ❌ Pendiente |

---

## app/Providers/ — Providers

| Archivo | Responsabilidad |
|---|---|
| `AppServiceProvider.php` | Registra bindings Interface → Implementación |
| `EventServiceProvider.php` | Registra qué Listener escucha qué Evento |

---

## resources/views/ — Vistas Blade

```
resources/views/
│
├── layouts/
│   ├── app.blade.php              # Layout general autenticado
│   ├── reserva.blade.php          # Layout del flujo de reservas
│   └── guest.blade.php            # Layout para login/register
│
├── admin/
│   ├── dashboard.blade.php
│   ├── empleados/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   ├── edit.blade.php
│   │   └── show.blade.php
│   ├── paquetes/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   ├── edit.blade.php
│   │   └── show.blade.php
│   ├── catalogos/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   ├── edit.blade.php
│   │   └── show.blade.php
│   └── reservas/
│       ├── index.blade.php
│       └── show.blade.php
│
├── cliente/
│   ├── dashboard.blade.php
│   ├── perfil.blade.php
│   └── reservas/
│       └── index.blade.php
│
├── fotografo/
│   ├── dashboard.blade.php
│   ├── calendario.blade.php
│   └── reservas/                  # ❌ Pendiente
│       ├── index.blade.php        # Reservas pendientes de aprobar
│       └── show.blade.php         # Detalle con botones aprobar/rechazar
│
├── reservas/                      # Flujo cliente (4 pasos)
│   ├── paso1.blade.php            # Catálogo y paquete
│   ├── paso2.blade.php            # Fecha, hora y descripción (Flatpickr)
│   ├── paso3.blade.php            # Datos del cliente
│   └── paso4.blade.php            # Resumen + modal confirmación
│
└── emails/                        # Plantillas de emails
    ├── reserva-nueva-fotografo.blade.php     ✅
    ├── reserva-aprobada-cliente.blade.php    ❌ Pendiente
    ├── reserva-rechazada-cliente.blade.php   ❌ Pendiente
    ├── pago-confirmado-cliente.blade.php     ❌ Pendiente
    ├── galeria-disponible-cliente.blade.php  ❌ Pendiente
    └── seleccion-confirmada-fotografo.blade.php ❌ Pendiente
```

---

## routes/web.php — Rutas

```
PÚBLICAS
├── GET  /                          Landing page
├── GET  /api/paquetes/{catalogo}   API paquetes por catálogo
└── GET  /disponibilidad/fechas     API fechas ocupadas

AUTENTICACIÓN
├── GET/POST  /login
├── POST      /logout
└── GET/POST  /register

CLIENTE  (middleware: auth, rol:CLIENTE, prefix: /cliente)
├── GET   /dashboard
├── GET   /perfil
├── PUT   /perfil
├── GET   /reservas
├── GET   /reservas/paso1
├── POST  /reservas/paso1
├── GET   /reservas/paso2
├── POST  /reservas/paso2
├── GET   /reservas/paso3
├── POST  /reservas/paso3
├── GET   /reservas/paso4
├── POST  /reservas/enviar
├── GET   /pagos/{reserva}          ❌ Pendiente
├── POST  /pagos/{reserva}          ❌ Pendiente
└── GET   /galeria/{sesion}         ❌ Pendiente

FOTÓGRAFO  (middleware: auth, rol:FOTOGRAFO, prefix: /fotografo)
├── GET   /dashboard
├── GET   /calendario
├── GET   /reservas-json
├── GET   /reservas                 ❌ Pendiente
├── GET   /reservas/{reserva}       ❌ Pendiente
├── POST  /reservas/{reserva}/aprobar   ❌ Pendiente
├── POST  /reservas/{reserva}/rechazar  ❌ Pendiente
└── POST  /sesiones/{sesion}/fotos      ❌ Pendiente

ADMINISTRADOR  (middleware: auth, rol:ADMINISTRADOR, prefix: /admin)
├── GET         /dashboard
├── Resource    /empleados
├── Resource    /paquetes
├── Resource    /catalogos
├── GET         /reservas
├── GET         /reservas/{reserva}
├── PATCH       /reservas/{reserva}/estado
├── GET         /nomina             ❌ Pendiente
├── POST        /nomina/calcular    ❌ Pendiente
└── POST        /nomina/confirmar   ❌ Pendiente
```

---

## Variables de entorno — .env

```env
# Base de datos
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=asstudio
DB_USERNAME=postgres
DB_PASSWORD=tu_password

# Email (Gmail SMTP)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tuestudio@gmail.com
MAIL_PASSWORD=xxxx xxxx xxxx xxxx   # App Password de Google, NO la contraseña real
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=tuestudio@gmail.com
MAIL_FROM_NAME="AS Studio"

# Almacenamiento S3
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=asstudio-fotos
```

---

## Estados del flujo de reserva

```
PENDIENTE           → cliente creó la reserva, espera revisión del fotógrafo
    ↓ fotógrafo aprueba
APROBADA            → fotógrafo aprobó, cliente debe pagar
    ↓ cliente paga
CONFIRMADA          → pago recibido, sesión confirmada
    ↓ sesión se realiza
COMPLETADA          → sesión realizada, fotógrafo sube fotos
    ↓ cliente selecciona fotos
SELECCION_PENDIENTE → galería disponible, cliente debe seleccionar
    ↓
ENTREGADA           → fotos editadas y entregadas

RECHAZADA           → fotógrafo rechazó la reserva
CANCELADA           → reserva cancelada
```

---

## Comandos útiles del proyecto

```bash
# Limpiar caché (hacer siempre que cambies .env o rutas)
php artisan config:clear
php artisan route:clear
php artisan cache:clear

# Ver todas las rutas
php artisan route:list

# Ver rutas de un módulo específico
php artisan route:list --path=cliente/reservas

# Crear una migración
php artisan make:migration create_pagos_table

# Correr migraciones
php artisan migrate

# Revisar la BD desde la consola
php artisan tinker

# Probar envío de email
php artisan tinker
>>> Mail::raw('Prueba', fn($m) => $m->to('destino@gmail.com')->subject('Test'));

# Ver logs en tiempo real
tail -f storage/logs/laravel.log   # Linux/Mac
type storage\logs\laravel.log      # Windows
```

---

## Convenciones del equipo

### Nombres de ramas GitHub
```
feature/AS-XX-descripcion-corta    # nueva funcionalidad
hotfix/AS-XX-descripcion-corta     # corrección urgente en main
```

### Mensajes de commit
```
AS-XX: descripción breve en presente
AS-15: implementar PagoService
AS-22: agregar vista galeria cliente
```

### Pull Requests
- Título: `AS-XX: Nombre de la historia`
- Descripción: qué hace, cómo probarlo
- Escribir `closes AS-XX` en la descripción para cerrar el ticket automáticamente

### Reglas de código
- Máximo 5 líneas de lógica en un Controller → si hay más, va al Service
- Todo acceso a BD pasa por el Repository, nunca `Model::create()` directo en Controller
- Cada cambio de estado de una entidad dispara un Event
- Los DTOs se construyen en el Controller con `fromRequest()`, no en el Service

---

*Última actualización: Mayo 2026 — Equipo AS-Studio*
