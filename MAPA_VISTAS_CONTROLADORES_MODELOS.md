# Mapa de vistas, controladores y modelos del proyecto

Este documento sirve como tabla de ubicacion rapida.
La idea es saber que vista pertenece a que modulo, que controlador la llama y que modelos usa por detras.

## 1. Resumen general

| Modulo | Vistas principales | Controlador | Modelos relacionados | Para que sirve |
|---|---|---|---|---|
| Landing publica | `welcome.blade.php` | Ruta directa en `routes/web.php` | No usa modelo directo | Pantalla inicial publica del sistema. |
| Login y autenticacion | `auth/login.blade.php`, `auth/register.blade.php`, `auth/forgot-password.blade.php`, `auth/reset-password.blade.php` | `AuthenticatedSessionController`, `RegisteredUserController`, `PasswordResetLinkController`, `NewPasswordController` | `User` | Inicio de sesion, registro y recuperacion de clave. |
| Perfil | `profile/edit.blade.php` y parciales de `profile/partials` | `ProfileController`, `PasswordController` | `User` | Editar datos del usuario, clave y eliminacion de cuenta. |
| Dashboard | `dashboard.blade.php` | `DashboardController` | `Plan`, `Meta`, `Indicador`, `Alineacion`, `Programa`, `Proyecto`, `ProyectoAvance`, `Entidad` | Muestra estadisticas, KPIs y graficas. |
| Entidades | `entidades/index.blade.php`, `create`, `edit` | `EntidadController` | `Entidad` | CRUD de entidades institucionales. |
| Programas | `programas/index.blade.php`, `create`, `edit` | `ProgramaController` | `Programa`, `Entidad` | CRUD de programas asociados a entidades. |
| Proyectos | `proyectos/index.blade.php`, `create`, `edit` | `ProyectoController` | `Proyecto`, `Entidad`, `Programa` | CRUD de proyectos asociados a entidad y programa. |
| Planes | `plans/index.blade.php`, `create`, `edit` | `PlanController` | `Plan`, `Pdn`, `Entidad` | CRUD de planes institucionales. |
| Metas CRUD | `metas/index.blade.php`, `create`, `edit` | `MetaController` | `Meta`, `Plan` | CRUD de metas asociadas a planes. |
| Indicadores CRUD | `indicadores/index.blade.php`, `create`, `edit` | `IndicadorController` | `Indicador`, `Meta` | CRUD de indicadores asociados a metas. |
| ODS | `ods/index.blade.php`, `create`, `edit` | `OdsController` | `Ods` | CRUD de catalogo ODS. |
| PDN / PND | `pdn/index.blade.php`, `create`, `edit` | `PdnController` | `Pdn` | CRUD de catalogo PDN/PND. |
| Objetivos estrategicos | `Objetivos_Estrategicos/index.blade.php`, `create`, `edit` | `ObjetivoEstrategicoController` | `ObjetivoEstrategico` | CRUD de objetivos estrategicos. |
| Alineaciones | `alineaciones/index.blade.php`, `create`, `edit` | `AlineacionController` | `Alineacion`, `Meta`, `Indicador`, `Ods`, `Pdn`, `ObjetivoEstrategico` | Relaciona metas/indicadores con instrumentos estrategicos. |
| Seguimiento de metas | `seguimiento/metas.blade.php`, `seguimiento/meta_show.blade.php` | `SeguimientoController` | `Meta`, `Indicador`, `IndicadorAvance` | Consulta avance de metas e indicadores. |
| Avances de indicadores | `seguimiento/indicador_avance.blade.php`, `indicador_avance_edit.blade.php` | `AvanceIndicadorController` | `Indicador`, `IndicadorAvance`, `User` | Registrar, editar y eliminar avances de indicadores. |
| Organizacion | `seguimiento/organizacion.blade.php`, `organizacion_entidad.blade.php` | `OrganizacionController` | `Entidad`, `Plan`, `Meta`, `Indicador`, `Programa`, `Proyecto` | Consulta estructura institucional por entidad. |
| Seguimiento de programas | `seguimiento/programa_show.blade.php` | `SeguimientoProgramaController` | `Programa`, `Proyecto`, `ProyectoAvance` | Muestra proyectos y avance promedio de un programa. |
| Seguimiento de proyectos | `seguimiento/proyecto_show.blade.php` | `SeguimientoProyectoController` | `Proyecto`, `ProyectoAvance`, `ProyectoAvanceEvidencia`, `User` | Muestra avance actual e historial de proyectos. |
| Avances y evidencias de proyecto | `seguimiento/proyecto_avance_create.blade.php`, `proyecto_avance_edit.blade.php`, seccion de evidencias en `proyecto_show.blade.php` | `ProyectoAvanceController` | `Proyecto`, `ProyectoAvance`, `ProyectoAvanceEvidencia`, `User` | Registrar avances, subir evidencias y administrar archivos. |
| Trazabilidad | `seguimiento/trazabilidad.blade.php` | `TrazabilidadController` | `Alineacion`, `Entidad`, `Meta`, `Ods`, `Pdn`, `ObjetivoEstrategico` | Consulta matriz de trazabilidad con filtros. |
| Reportes | `reportes/index.blade.php`, `institucional`, `metas`, `proyectos`, `trazabilidad` | `ReporteController` | `Entidad`, `Programa`, `Proyecto`, `Plan`, `Meta`, `Indicador`, `Alineacion` | Generacion de reportes consultivos e imprimibles. |
| Auditoria | `auditoria/index.blade.php` | `AuditLogController` | `AuditLog`, `User` | Consulta acciones registradas por usuarios. |
| Usuarios | `usuarios/index.blade.php`, `create`, `edit` | `UserController` | `User` | Administracion de usuarios y roles. |

## 2. Mapa por modulo con mas detalle

### Landing publica

| Archivo | Relacion |
|---|---|
| `resources/views/welcome.blade.php` | Se carga desde la ruta `/` en `routes/web.php`. |
| `resources/css/app.css` | Contiene estilos de la landing en clases como `.landing-page`, `.landing-hero`, `.landing-preview`. |
| `resources/views/components/application-logo.blade.php` | Logo usado en landing, login y sidebar. |

Esta pantalla no usa controlador propio.
La ruta devuelve la vista directamente:

```php
Route::get('/', function () {
    return view('welcome');
});
```

### Login, registro y recuperacion

| Vista | Controlador | Modelo | Explicacion |
|---|---|---|---|
| `auth/login.blade.php` | `AuthenticatedSessionController` | `User` | Muestra formulario de correo y clave. |
| `auth/register.blade.php` | `RegisteredUserController` | `User` | Crea usuario nuevo con rol `consulta`. |
| `auth/forgot-password.blade.php` | `PasswordResetLinkController` | `User` | Solicita correo para enviar enlace de recuperacion. |
| `auth/reset-password.blade.php` | `NewPasswordController` | `User` | Permite escribir nueva clave usando token. |
| `auth/confirm-password.blade.php` | `ConfirmablePasswordController` | `User` | Confirma clave en zonas protegidas. |
| `auth/verify-email.blade.php` | `EmailVerificationPromptController` | `User` | Vista de verificacion de correo. |

Rutas principales:

```text
routes/auth.php
```

Archivos de traduccion relacionados:

```text
lang/es/auth.php
lang/es/passwords.php
lang/es/validation.php
lang/es.json
```

### Perfil

| Vista | Controlador | Modelo | Explicacion |
|---|---|---|---|
| `profile/edit.blade.php` | `ProfileController` | `User` | Vista principal del perfil. |
| `profile/partials/update-profile-information-form.blade.php` | `ProfileController@update` | `User` | Actualiza nombre y correo. |
| `profile/partials/update-password-form.blade.php` | `PasswordController@update` | `User` | Actualiza clave. |
| `profile/partials/delete-user-form.blade.php` | `ProfileController@destroy` | `User` | Elimina cuenta del usuario. |

El perfil usa componentes Blade:

```text
resources/views/components/input-label.blade.php
resources/views/components/text-input.blade.php
resources/views/components/input-error.blade.php
resources/views/components/primary-button.blade.php
resources/views/components/danger-button.blade.php
resources/views/components/modal.blade.php
```

### Dashboard

| Vista | Controlador | Modelos | Explicacion |
|---|---|---|---|
| `dashboard.blade.php` | `DashboardController@index` | `Plan`, `Meta`, `Indicador`, `Alineacion`, `Programa`, `Proyecto`, `ProyectoAvance`, `Entidad` | Calcula KPIs, porcentajes, ranking por entidad, grafica mensual y actividad reciente. |

El controlador arma datos como:

- planes activos
- total de metas
- total de indicadores
- progreso institucional
- porcentaje de alineacion
- avance de proyectos
- avance por entidad
- actividad mensual

La vista solo muestra esos datos con tarjetas, barras y graficas CSS.

### Entidades

| Vista | Controlador | Modelo | Explicacion |
|---|---|---|---|
| `entidades/index.blade.php` | `EntidadController@index` | `Entidad` | Lista entidades. |
| `entidades/create.blade.php` | `EntidadController@create` y `store` | `Entidad` | Formulario para crear entidad. |
| `entidades/edit.blade.php` | `EntidadController@edit` y `update` | `Entidad` | Formulario para editar entidad. |

Relaciones del modelo:

```php
Entidad hasMany Plan
Entidad hasMany Programa
Entidad hasMany Proyecto
```

### Programas

| Vista | Controlador | Modelos | Explicacion |
|---|---|---|---|
| `programas/index.blade.php` | `ProgramaController@index` | `Programa` | Lista programas. |
| `programas/create.blade.php` | `ProgramaController@create` y `store` | `Programa`, `Entidad` | Crea programa y lo asocia a una entidad. |
| `programas/edit.blade.php` | `ProgramaController@edit` y `update` | `Programa`, `Entidad` | Edita programa. |

Relaciones:

```php
Programa belongsTo Entidad
Programa hasMany Proyecto
```

### Proyectos

| Vista | Controlador | Modelos | Explicacion |
|---|---|---|---|
| `proyectos/index.blade.php` | `ProyectoController@index` | `Proyecto`, `Entidad`, `Programa` | Lista proyectos con entidad y programa. |
| `proyectos/create.blade.php` | `ProyectoController@create` y `store` | `Proyecto`, `Entidad`, `Programa` | Crea proyecto asociado a entidad/programa. |
| `proyectos/edit.blade.php` | `ProyectoController@edit` y `update` | `Proyecto`, `Entidad`, `Programa` | Edita proyecto. |

Relaciones:

```php
Proyecto belongsTo Entidad
Proyecto belongsTo Programa
Proyecto hasMany ProyectoAvance
Proyecto hasOne ultimoAvance
```

### Planes

| Vista | Controlador | Modelos | Explicacion |
|---|---|---|---|
| `plans/index.blade.php` | `PlanController@index` | `Plan`, `Pdn`, `Entidad` | Lista planes con PDN y entidad. |
| `plans/create.blade.php` | `PlanController@create` y `store` | `Plan`, `Pdn`, `Entidad` | Crea plan. |
| `plans/edit.blade.php` | `PlanController@edit` y `update` | `Plan`, `Pdn`, `Entidad` | Edita plan. |

Relaciones:

```php
Plan belongsTo Pdn
Plan belongsTo Entidad
Plan hasMany Meta
```

### Metas CRUD

| Vista | Controlador | Modelos | Explicacion |
|---|---|---|---|
| `metas/index.blade.php` | `MetaController@index` | `Meta`, `Plan` | Lista metas con plan. |
| `metas/create.blade.php` | `MetaController@create` y `store` | `Meta`, `Plan` | Crea meta asociada a plan. |
| `metas/edit.blade.php` | `MetaController@edit` y `update` | `Meta`, `Plan` | Edita meta. |

Relaciones:

```php
Meta belongsTo Plan
Meta hasMany Indicador
Meta hasMany Alineacion
```

### Indicadores CRUD

| Vista | Controlador | Modelos | Explicacion |
|---|---|---|---|
| `indicadores/index.blade.php` | `IndicadorController@index` | `Indicador`, `Meta` | Lista indicadores con meta. |
| `indicadores/create.blade.php` | `IndicadorController@create` y `store` | `Indicador`, `Meta` | Crea indicador asociado a meta. |
| `indicadores/edit.blade.php` | `IndicadorController@edit` y `update` | `Indicador`, `Meta` | Edita indicador. |

Relaciones:

```php
Indicador belongsTo Meta
Indicador hasMany IndicadorAvance
Indicador hasOne ultimoAvance
```

### Catalogos ODS, PDN y objetivos

| Modulo | Vistas | Controlador | Modelo | Explicacion |
|---|---|---|---|---|
| ODS | `ods/index.blade.php`, `create`, `edit` | `OdsController` | `Ods` | Catalogo de ODS. |
| PDN / PND | `pdn/index.blade.php`, `create`, `edit` | `PdnController` | `Pdn` | Catalogo de plan nacional/desarrollo. |
| Objetivos estrategicos | `Objetivos_Estrategicos/index.blade.php`, `create`, `edit` | `ObjetivoEstrategicoController` | `ObjetivoEstrategico` | Catalogo de objetivos estrategicos. |

Estos catalogos se usan principalmente en alineaciones y trazabilidad.

### Alineaciones

| Vista | Controlador | Modelos | Explicacion |
|---|---|---|---|
| `alineaciones/index.blade.php` | `AlineacionController@index` | `Alineacion`, `Meta`, `Indicador`, `Ods`, `Pdn`, `ObjetivoEstrategico` | Lista relaciones estrategicas. |
| `alineaciones/create.blade.php` | `AlineacionController@create` y `store` | `Meta`, `Indicador`, `Ods`, `Pdn`, `ObjetivoEstrategico`, `Alineacion` | Crea una alineacion. |
| `alineaciones/edit.blade.php` | `AlineacionController@edit` y `update` | `Alineacion` y catalogos | Edita alineacion. |

Relaciones:

```php
Alineacion belongsTo Meta
Alineacion belongsTo Indicador
Alineacion belongsTo Ods
Alineacion belongsTo Pdn
Alineacion belongsTo ObjetivoEstrategico
```

## 3. Seguimiento y evidencias

### Seguimiento de metas

| Vista | Controlador | Modelos | Explicacion |
|---|---|---|---|
| `seguimiento/metas.blade.php` | `SeguimientoController@index` | `Meta`, `Plan`, `Indicador`, `IndicadorAvance` | Lista metas con avance. |
| `seguimiento/meta_show.blade.php` | `SeguimientoController@show` | `Meta`, `Indicador`, `IndicadorAvance` | Muestra detalle de una meta, indicadores y ultimo avance. |

La vista `meta_show` muestra botones segun rol:

```php
auth()->user()->canRegisterSeguimiento()
```

Eso muestra `Registrar avance` solo a admin y tecnico.

### Avances de indicadores

| Vista | Controlador | Modelos | Explicacion |
|---|---|---|---|
| `seguimiento/indicador_avance.blade.php` | `AvanceIndicadorController@create` y `store` | `Indicador`, `IndicadorAvance`, `User` | Formulario para registrar avance de indicador. |
| `seguimiento/indicador_avance_edit.blade.php` | `AvanceIndicadorController@edit` y `update` | `IndicadorAvance`, `Indicador`, `User` | Edita avance de indicador. |

El controlador guarda:

```php
'indicador_id' => $indicador->id
'user_id' => Auth::id()
'fecha'
'valor_reportado'
'comentario'
'evidencia_path'
```

La evidencia se guarda como archivo en storage y la ruta queda en `evidencia_path`.

### Organizacion

| Vista | Controlador | Modelos | Explicacion |
|---|---|---|---|
| `seguimiento/organizacion.blade.php` | `OrganizacionController@index` | `Entidad`, `Plan`, `Meta`, `Indicador`, `Programa`, `Proyecto` | Lista entidades con KPIs. |
| `seguimiento/organizacion_entidad.blade.php` | `OrganizacionController@show` | `Entidad`, `Programa`, `Proyecto`, `Plan`, `Meta` | Detalle de una entidad. |

### Seguimiento de programas

| Vista | Controlador | Modelos | Explicacion |
|---|---|---|---|
| `seguimiento/programa_show.blade.php` | `SeguimientoProgramaController@show` | `Programa`, `Proyecto`, `ProyectoAvance` | Muestra proyectos de un programa y avance promedio. |

### Seguimiento de proyectos

| Vista | Controlador | Modelos | Explicacion |
|---|---|---|---|
| `seguimiento/proyecto_show.blade.php` | `SeguimientoProyectoController@show` | `Proyecto`, `ProyectoAvance`, `ProyectoAvanceEvidencia`, `User` | Muestra avance actual, historial y evidencias. |
| `seguimiento/proyecto_avance_create.blade.php` | `ProyectoAvanceController@create` y `store` | `Proyecto`, `ProyectoAvance`, `ProyectoAvanceEvidencia`, `User` | Registra avance de proyecto y evidencias iniciales. |
| `seguimiento/proyecto_avance_edit.blade.php` | `ProyectoAvanceController@edit` y `update` | `ProyectoAvance`, `Proyecto`, `ProyectoAvanceEvidencia` | Edita avance de proyecto. |

En `proyecto_show.blade.php` tambien se muestran evidencias.
Las evidencias usan:

```text
ProyectoAvanceEvidencia
```

Y se administran con:

```text
ProyectoAvanceController@addEvidencia
ProyectoAvanceController@deleteEvidencia
```

Importante:

- `ProyectoAvance` es el avance principal.
- `ProyectoAvanceEvidencia` es cada archivo subido para respaldar ese avance.

Relaciones:

```php
ProyectoAvance belongsTo Proyecto
ProyectoAvance belongsTo User
ProyectoAvance hasMany ProyectoAvanceEvidencia
ProyectoAvanceEvidencia belongsTo ProyectoAvance
```

## 4. Trazabilidad, reportes y auditoria

### Trazabilidad

| Vista | Controlador | Modelos | Explicacion |
|---|---|---|---|
| `seguimiento/trazabilidad.blade.php` | `TrazabilidadController@index` | `Alineacion`, `Entidad`, `Meta`, `Ods`, `Pdn`, `ObjetivoEstrategico` | Muestra matriz de relaciones con filtros y KPIs. |

La trazabilidad se apoya en el modelo:

```text
Alineacion
```

porque ahi se guardan las relaciones entre meta/indicador y ODS/PDN/objetivo.

### Reportes

| Vista | Controlador | Modelos | Explicacion |
|---|---|---|---|
| `reportes/index.blade.php` | `ReporteController@index` | No calcula modelos directamente | Menu principal de reportes. |
| `reportes/institucional.blade.php` | `ReporteController@institucional` | `Entidad`, `Programa`, `Proyecto`, `Plan`, `Meta`, `Indicador`, `Alineacion` | Resumen institucional. |
| `reportes/metas.blade.php` | `ReporteController@metas` | `Meta`, `Entidad`, `Indicador` | Reporte filtrable de metas. |
| `reportes/proyectos.blade.php` | `ReporteController@proyectos` | `Proyecto`, `Entidad`, `Programa`, `ProyectoAvance`, `ProyectoAvanceEvidencia` | Reporte de proyectos y evidencias. |
| `reportes/trazabilidad.blade.php` | `ReporteController@trazabilidad` | `Alineacion`, `Meta`, `Indicador`, `Ods`, `Pdn`, `ObjetivoEstrategico` | Reporte de trazabilidad. |
| `reportes/partials/header.blade.php` | Incluido por vistas de reportes | No usa modelo directo | Encabezado comun con boton imprimir/PDF. |

### Auditoria

| Vista | Controlador | Modelos | Explicacion |
|---|---|---|---|
| `auditoria/index.blade.php` | `AuditLogController@index` | `AuditLog`, `User` | Lista acciones registradas con filtros. |

La auditoria se alimenta del middleware:

```text
app/Http/Middleware/AuditMiddleware.php
```

Ese middleware registra acciones de tipo:

- crear
- actualizar
- eliminar

## 5. Usuarios y roles

| Vista | Controlador | Modelo | Explicacion |
|---|---|---|---|
| `usuarios/index.blade.php` | `UserController@index` | `User` | Lista usuarios y muestra rol. |
| `usuarios/create.blade.php` | `UserController@create` y `store` | `User` | Crea usuario y asigna rol. |
| `usuarios/edit.blade.php` | `UserController@edit` y `update` | `User` | Edita usuario, rol y clave opcional. |

El modelo `User` contiene:

```php
ROLE_ADMIN
ROLE_PLANIFICACION
ROLE_TECNICO
ROLE_CONSULTA
ROLE_LABELS
```

La vista usa:

```php
User::ROLE_LABELS
```

para llenar el select de roles.

## 6. Layouts y componentes

| Archivo | Relacion | Explicacion |
|---|---|---|
| `layouts/app.blade.php` | Usado por vistas internas con `<x-app-layout>` | Estructura general con sidebar y contenido. |
| `layouts/navigation.blade.php` | Incluido en `app.blade.php` | Barra lateral con menus segun rol. |
| `layouts/guest.blade.php` | Usado por vistas auth con `<x-guest-layout>` | Layout para login, registro y recuperacion. |
| `components/application-logo.blade.php` | Usado en landing, login y sidebar | Logo propio del sistema. |
| `components/input-label.blade.php` | Formularios | Etiquetas de campos. |
| `components/text-input.blade.php` | Formularios | Inputs reutilizables. |
| `components/input-error.blade.php` | Formularios | Muestra errores de validacion. |
| `components/primary-button.blade.php` | Formularios | Boton principal. |
| `components/danger-button.blade.php` | Perfil | Boton peligroso, por ejemplo eliminar cuenta. |
| `components/modal.blade.php` | Perfil | Modal de confirmacion. |

## 7. Rutas principales y su modulo

| Ruta aproximada | Controlador | Vista |
|---|---|---|
| `/` | Ruta directa | `welcome.blade.php` |
| `/login` | `AuthenticatedSessionController` | `auth/login.blade.php` |
| `/dashboard` | `DashboardController` | `dashboard.blade.php` |
| `/entidades` | `EntidadController` | `entidades/index.blade.php` |
| `/programas` | `ProgramaController` | `programas/index.blade.php` |
| `/proyectos` | `ProyectoController` | `proyectos/index.blade.php` |
| `/plans` | `PlanController` | `plans/index.blade.php` |
| `/metas` | `MetaController` | `metas/index.blade.php` |
| `/indicadores` | `IndicadorController` | `indicadores/index.blade.php` |
| `/alineaciones` | `AlineacionController` | `alineaciones/index.blade.php` |
| `/seguimiento/metas` | `SeguimientoController` | `seguimiento/metas.blade.php` |
| `/seguimiento/metas/{meta}` | `SeguimientoController` | `seguimiento/meta_show.blade.php` |
| `/seguimiento/proyectos/{proyecto}` | `SeguimientoProyectoController` | `seguimiento/proyecto_show.blade.php` |
| `/seguimiento/trazabilidad` | `TrazabilidadController` | `seguimiento/trazabilidad.blade.php` |
| `/reportes` | `ReporteController` | `reportes/index.blade.php` |
| `/auditoria` | `AuditLogController` | `auditoria/index.blade.php` |
| `/usuarios` | `UserController` | `usuarios/index.blade.php` |

## 8. Como explicarlo al profesor

Una forma sencilla de explicarlo:

> En Laravel, la ruta recibe la peticion y llama a un controlador. El controlador consulta o modifica modelos, y luego devuelve una vista Blade. Las vistas muestran la informacion que el controlador les envia. Los modelos representan las tablas de la base de datos y definen relaciones como `belongsTo`, `hasMany` o `hasOne`.

Ejemplo con proyectos:

1. El usuario entra a `/proyectos`.
2. `routes/web.php` manda la peticion a `ProyectoController@index`.
3. El controlador consulta `Proyecto::with(['entidad', 'programa'])`.
4. Eso usa los modelos `Proyecto`, `Entidad` y `Programa`.
5. El controlador devuelve `proyectos.index`.
6. La vista muestra la tabla con proyectos, entidad y programa.

Ejemplo con evidencias:

1. El usuario entra al seguimiento de un proyecto.
2. `SeguimientoProyectoController@show` carga el proyecto con sus avances y evidencias.
3. La vista `seguimiento/proyecto_show.blade.php` muestra el historial.
4. Si el usuario agrega evidencia, la peticion va a `ProyectoAvanceController@addEvidencia`.
5. Ese controlador guarda el archivo y crea un registro en `ProyectoAvanceEvidencia`.
6. La evidencia queda relacionada con un avance de proyecto.

