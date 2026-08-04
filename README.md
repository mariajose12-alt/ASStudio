# AS Studio

Plataforma de gestión integral para un estudio fotográfico: reservas, pagos, galerías de entrega, nómina de fotógrafos y renta del estudio físico, todo en un solo sistema con cuatro roles de usuario distintos.

Construido con **Laravel 12**, **PostgreSQL**, **Blade** y **Tailwind CSS**, siguiendo una arquitectura en capas (`Controller → DTO → Service → Repository → Model`).

---

## Índice

- [Roles del sistema](#roles-del-sistema)
- [Funcionalidades por módulo](#funcionalidades-por-módulo)
- [Stack técnico](#stack-técnico)
- [Arquitectura](#arquitectura)
- [Variables de entorno relevantes](#variables-de-entorno-relevantes)
- [Comandos programados](#comandos-programados-scheduler)
- [Estructura de carpetas](#estructura-de-carpetas)

---

## Roles del sistema

El sistema maneja cuatro roles, cada uno con su propio panel y layout:

| Rol | Descripción |
|---|---|
| **Cliente** | Reserva sesiones, sube comprobantes de pago, selecciona sus fotos favoritas y descarga la galería final. |
| **Fotógrafo** | Gestiona sus reservas, calendario de sesiones, sube y entrega fotografías, ve su nómina. |
| **Socio (Estudio)** | Administra la renta del estudio físico: bloqueos de horario, aprobación/rechazo de solicitudes de renta. |
| **Administrador** | Control total: catálogos, paquetes, empleados, pagos, nómina, metas mensuales y solicitudes de estudio. |

---

## Funcionalidades por módulo

### Autenticación
- Registro con verificación de correo (link de activación con expiración).
- Inicio de sesión con **Google OAuth** (Socialite), con creación automática de cuenta.
- Opción de "recordarme" y de establecer contraseña incluso si la cuenta se creó con Google.
- Recuperación de contraseña por correo.
- Activación de cuentas de empleados (fotógrafos) por invitación.
- Roles y permisos vía middleware (`CheckRol`, `CheckVerificado`).

### Reservas de sesiones fotográficas
- Wizard de reserva en 4 pasos (catálogo/paquete → fecha y hora → datos de contacto → resumen y confirmación).
- Reservas en estudio o en exteriores (con dirección).
- Validación de disponibilidad del fotógrafo por fecha/hora.
- El fotógrafo puede **aprobar**, **rechazar** o **proponer una modificación** (fecha/hora/lugar distinto) a una reserva, con motivos predefinidos seleccionables además de texto libre.
- Notificaciones automáticas por correo y WhatsApp (Twilio) en cada cambio de estado.

### Pagos y comprobantes
- Subida de comprobante de transferencia bancaria por parte del cliente (con drag & drop y validación de tamaño en el navegador).
- **Lectura automática (OCR con Tesseract)** del comprobante para validar montos contra bancos dominicanos.
- Flujo de anticipo y pago completo.
- El administrador aprueba o rechaza comprobantes, con motivos predefinidos + texto libre.
- Reembolsos.

### Galería y entrega de fotos
- El fotógrafo sube fotos originales y editadas por sesión, con estado de flujo (`CONFIRMADA → EN_PROCESO → GALERIA_DISPONIBLE → EN_EDICION → FINALIZADA`).
- Cliente navega la galería con lightbox, zoom táctil (pinch-to-zoom) y navegación por swipe en móvil.
- Cliente marca sus fotos favoritas/selección para edición.
- Exportación de la selección del cliente a **CSV** para el flujo de edición del fotógrafo.
- Descarga individual o de la galería completa en **ZIP**.
- Almacenamiento de imágenes en **Cloudflare R2** (S3-compatible) con URLs firmadas.

### Renta del estudio (espacio físico)
- Solicitud pública de renta de estudio (fecha, horario, cantidad de personas, iluminación, fondo, finalidad).
- Calendario de bloqueos y disponibilidad.
- Aprobación/rechazo por parte de socios y administrador, con motivos predefinidos.
- Notificaciones a todos los socios ante una nueva solicitud.

### Nómina
- Cálculo automático de nómina para fotógrafos según parámetros legales configurables (AFP, SFS, riesgo laboral, topes de cotización, ISR por tramos, incentivos por ventas).
- Disputas de nómina (el fotógrafo puede objetar un cálculo; el administrador acepta o rechaza).
- Confirmación y pago de nómina, generación de PDF.
- Vencimiento automático de disputas no atendidas (comando programado diario).

### Catálogos y paquetes
- CRUD de catálogos fotográficos y paquetes asociados (cantidad de fotos incluidas, precio).
- Selección de paquete con formato claro (`Paquete — Fotos X — Precio`) durante la reserva.

### Empleados y ayudantes
- Gestión de empleados (fotógrafos, socios, administradores).
- Solicitudes de ayudante fotógrafo para sesiones grandes, con postulación y confirmación.
- Metas mensuales de ventas por fotógrafo.

### Notificaciones
- Centro de notificaciones en el navbar (con contador de no leídas) para todos los roles.
- Limpieza automática de notificaciones antiguas (comando programado diario).

---

## Stack técnico

| Categoría | Tecnología |
|---|---|
| Backend | PHP 8.2+ / Laravel 12 |
| Base de datos | PostgreSQL |
| Frontend | Blade + Tailwind CSS 4 + Vite |
| Almacenamiento de archivos | Cloudflare R2 (vía `league/flysystem-aws-s3-v3`) |
| Autenticación social | Google OAuth (`laravel/socialite`) |
| OCR de comprobantes | Tesseract (`thiagoalessio/tesseract_ocr`) |
| Notificaciones WhatsApp | Twilio (Content API / plantillas) |
| Correo | SMTP (Gmail) |
| Manejo de imágenes | Intervention Image |

---

## Arquitectura

El proyecto sigue una arquitectura en capas estricta:

```
HTTP Request
    ↓
Controller     → solo recibe y responde HTTP, sin lógica de negocio
    ↓
DTO            → transporta datos de forma segura entre capas
    ↓
Service        → toda la lógica de negocio vive aquí
    ↓
Repository     → solo consultas y escrituras a la base de datos
    ↓
Model          → tablas, relaciones y atributos (Eloquent)
    ↓
PostgreSQL
```

Además se usan:
- **Events + Listeners** para desacoplar acciones secundarias (ej. una reserva creada dispara la notificación al fotógrafo).
- **Strategy pattern** para pasarelas de pago (`PagoGatewayInterface`), pensado para integrar AZUL/Cardnet más adelante.
- **Value Objects** para conceptos del dominio.

---

## Variables de entorno relevantes

Además de las variables estándar de Laravel (`DB_*`, `MAIL_*`, `APP_*`), este proyecto necesita:

```env
# Cloudflare R2 (compatible con S3)
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=auto
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

# Google OAuth
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=

# Tesseract OCR
TESSERACT_TESSDATA_PATH=

# Twilio (WhatsApp)
TWILIO_ACCOUNT_SID=
TWILIO_AUTH_TOKEN=
TWILIO_FROM=
WA_TEMPLATE_RESERVA_APROBADA=
WA_TEMPLATE_RESERVA_RECHAZADA=
WA_TEMPLATE_RESERVA_MODIFICADA=
WA_TEMPLATE_RECORDATORIO_SESION=
WA_TEMPLATE_SOLICITUD_ESTUDIO_APROBADA=
WA_TEMPLATE_SOLICITUD_ESTUDIO_RECHAZADA=
WA_TEMPLATE_NUEVA_SOLICITUD_ESTUDIO_SOCIOS=
```

---

## Comandos programados (scheduler)

```
nomina::confirmar-vencidas     → diario, vence disputas de nómina sin atender
notificaciones:limpiar         → diario, limpia notificaciones antiguas
```

Requiere tener corriendo el scheduler de Laravel (`php artisan schedule:work` en desarrollo, o un cron real en producción):

```bash
* * * * * php /ruta/al/proyecto/artisan schedule:run >> /dev/null 2>&1
```

---

## Estructura de carpetas

```
app/
├── DTOs/                  # Transporte de datos entre capas
├── Events/ Listeners/     # Acciones desacopladas (emails, notificaciones)
├── Http/
│   ├── Controllers/       # Un controller por recurso/rol
│   │   └── Auth/          # Login, registro, Google OAuth
│   ├── Middleware/        # CheckRol, CheckVerificado, SecurityHeaders
│   └── Requests/          # Form Requests (validaciones)
├── Mail/                  # Mailables
├── Models/                # Eloquent (Usuario, Reserva, Sesion, Pago, Nomina, etc.)
├── Repositories/          # Acceso a datos (con sus Contracts)
├── Services/               # Lógica de negocio
└── Strategies/              # Ej. pasarelas de pago

database/
├── migrations/             # 75+ migraciones
└── seeders/

resources/views/
├── admin/                  # Panel de administrador
├── cliente/                 # Panel de cliente (reservas, pagos, galería, perfil)
├── fotografo/                # Panel de fotógrafo (calendario, sesiones, nómina)
├── socio/                     # Panel de socio (estudio)
├── emails/                     # Plantillas de correo
├── reservas/                    # Wizard de reserva (pasos 1-4)
└── layouts/                      # Un layout por rol/contexto

routes/
├── web.php                 # Rutas, agrupadas y nombradas por rol
├── auth.php
└── console.php              # Comandos programados
```

---

## Equipo

Proyecto desarrollado en **PUCMM** (Pontificia Universidad Católica Madre y Maestra) bajo metodología Scrum, por María José Cruz, Almy Ventura y Almer Álvarez.
