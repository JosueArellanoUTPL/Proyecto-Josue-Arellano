# Manual del sistema de roles y permisos

Este manual explica como funcionan los roles en el proyecto: donde se crearon, como se guardan, como se asignan a usuarios, como se protegen rutas y como se muestran u ocultan opciones en las vistas.

La idea es poder explicar el proceso completo con palabras simples y con archivos reales del proyecto.

## 1. Para que sirven los roles

Los roles sirven para controlar que puede hacer cada usuario dentro del sistema.

En este proyecto se manejan 4 roles:

- **Administrador del Sistema**
- **Responsable de Planificacion**
- **Tecnico de Seguimiento**
- **Autoridad / Consulta**

La idea general es:

- El administrador puede hacer todo.
- Planificacion administra datos base como entidades, planes, metas, indicadores y alineaciones.
- Tecnico registra avances y evidencias.
- Consulta solo revisa informacion, dashboard, seguimiento, trazabilidad y reportes.

## 2. Donde se guarda el rol en base de datos

El rol se guarda en la tabla:

```text
users
```

Campo:

```text
role
```

Ese campo fue agregado por una migracion.

Archivo:

```text
database/migrations/2026_01_03_213155_add_role_to_users_table_v2.php
```

Parte importante:

```php
$table->string('role')->default('tecnico')->after('email');
```

Que significa:

- Agrega una columna llamada `role`.
- Es de tipo texto.
- Al inicio tenia valor por defecto `tecnico`.
- Se ubica despues del campo `email`.

Luego se ajusto el valor por defecto con otra migracion.

Archivo:

```text
database/migrations/2026_01_24_010000_update_users_role_default_for_four_roles.php
```

Parte importante:

```php
DB::statement("ALTER TABLE users MODIFY role VARCHAR(255) NOT NULL DEFAULT 'consulta'");
```

Que significa:

- Cambia el valor por defecto del campo `role`.
- Ahora, si se crea un usuario sin elegir rol, queda como `consulta`.
- Esto es mas seguro porque un usuario nuevo solo puede consultar, no modificar.

## 3. Donde estan definidos los roles en el codigo

Archivo:

```text
app/Models/User.php
```

Aqui estan los roles reales del sistema:

```php
public const ROLE_ADMIN = 'admin';
public const ROLE_PLANIFICACION = 'planificacion';
public const ROLE_TECNICO = 'tecnico';
public const ROLE_CONSULTA = 'consulta';
```

Que significa:

- En la base de datos no se guarda el nombre largo del rol.
- Se guarda una clave corta:
  - `admin`
  - `planificacion`
  - `tecnico`
  - `consulta`

Tambien estan las etiquetas para mostrar en pantalla:

```php
public const ROLE_LABELS = [
    self::ROLE_ADMIN => 'Administrador del Sistema',
    self::ROLE_PLANIFICACION => 'Responsable de Planificacion',
    self::ROLE_TECNICO => 'Tecnico de Seguimiento',
    self::ROLE_CONSULTA => 'Autoridad / Consulta',
];
```

Que significa:

- Si en base esta guardado `admin`, en pantalla se muestra `Administrador del Sistema`.
- Si en base esta guardado `consulta`, en pantalla se muestra `Autoridad / Consulta`.

Esto ayuda a que el codigo use claves simples, pero la interfaz muestre nombres entendibles.

## 4. Por que el campo role se puede guardar

En el modelo `User` existe:

```php
protected $fillable = [
    'name',
    'email',
    'password',
    'role',
];
```

Que significa:

- Laravel permite guardar esos campos con `create()` o `update()`.
- Si `role` no estuviera aqui, Laravel no dejaria asignarlo masivamente.
- Por eso es necesario incluirlo.

## 5. Metodos importantes del modelo User

Archivo:

```text
app/Models/User.php
```

### `roleKeys`

```php
public static function roleKeys(): array
{
    return array_keys(self::ROLE_LABELS);
}
```

Que hace:

- Devuelve las claves permitidas:

```text
admin, planificacion, tecnico, consulta
```

Se usa para validar que no se guarde un rol inventado.

### `roleLabel`

```php
public function roleLabel(): string
{
    return self::ROLE_LABELS[$this->role] ?? $this->role;
}
```

Que hace:

- Convierte el rol guardado en base a una etiqueta visible.
- Por ejemplo:
  - `admin` se muestra como `Administrador del Sistema`.
  - `tecnico` se muestra como `Tecnico de Seguimiento`.

Se usa en vistas como:

```text
resources/views/layouts/navigation.blade.php
resources/views/usuarios/index.blade.php
```

### `isAdmin`

```php
public function isAdmin(): bool
{
    return $this->role === self::ROLE_ADMIN;
}
```

Que hace:

- Devuelve `true` si el usuario es administrador.
- Sirve para mostrar opciones solo de administrador o permitir acciones especiales.

### `canManagePlanning`

```php
public function canManagePlanning(): bool
{
    return in_array($this->role, [
        self::ROLE_ADMIN,
        self::ROLE_PLANIFICACION,
    ], true);
}
```

Que hace:

- Devuelve `true` si el usuario puede administrar planificacion.
- Permite a:
  - admin
  - planificacion

Se usa para mostrar el menu de planificacion en la barra lateral.

### `canRegisterSeguimiento`

```php
public function canRegisterSeguimiento(): bool
{
    return in_array($this->role, [
        self::ROLE_ADMIN,
        self::ROLE_TECNICO,
    ], true);
}
```

Que hace:

- Devuelve `true` si el usuario puede registrar avances.
- Permite a:
  - admin
  - tecnico

Se usa en vistas de seguimiento para mostrar botones como `Registrar avance`.

## 6. Como se asigna un rol a un usuario

### Desde el modulo Usuarios

El modulo de usuarios esta protegido para administrador.

Controlador:

```text
app/Http/Controllers/UserController.php
```

Vista para crear:

```text
resources/views/usuarios/create.blade.php
```

Vista para editar:

```text
resources/views/usuarios/edit.blade.php
```

En la vista de crear usuario aparece este select:

```blade
<select name="role" class="w-full border rounded px-3 py-2">
    @foreach(\App\Models\User::ROLE_LABELS as $role => $label)
        <option value="{{ $role }}" {{ old('role') === $role ? 'selected' : '' }}>
            {{ $label }}
        </option>
    @endforeach
</select>
```

Que significa:

- Recorre `User::ROLE_LABELS`.
- Cada opcion tiene como valor la clave del rol.
- Muestra la etiqueta bonita.

Ejemplo:

```html
value="admin" -> Administrador del Sistema
value="tecnico" -> Tecnico de Seguimiento
```

Cuando se envia el formulario, el rol llega al controlador como:

```php
$request->role
```

### Validacion al crear usuario

Archivo:

```text
app/Http/Controllers/UserController.php
```

Metodo:

```php
public function store(Request $request)
```

Parte importante:

```php
$data = $request->validate([
    'name' => 'required|string|max:150',
    'email' => 'required|email|max:255|unique:users,email',
    'password' => 'required|string|min:6|confirmed',
    'role' => ['required', Rule::in(User::roleKeys())],
]);
```

Que significa:

- El nombre es obligatorio.
- El correo es obligatorio y unico.
- La clave es obligatoria y debe confirmarse.
- El rol es obligatorio.
- El rol debe estar dentro de los roles permitidos por `User::roleKeys()`.

Esto evita que alguien mande un rol falso desde el navegador.

Luego se crea el usuario:

```php
User::create([
    'name' => $data['name'],
    'email' => $data['email'],
    'password' => Hash::make($data['password']),
    'role' => $data['role'],
]);
```

Que significa:

- Guarda nombre.
- Guarda correo.
- Guarda clave hasheada.
- Guarda rol.

### Validacion al editar usuario

Metodo:

```php
public function update(Request $request, User $usuario)
```

Parte importante:

```php
'role' => ['required', Rule::in(User::roleKeys())],
```

Esto vuelve a validar que el rol elegido exista.

Luego:

```php
$usuario->role = $data['role'];
$usuario->save();
```

Esto actualiza el rol del usuario.

## 7. Rol por defecto en registro publico

Cuando alguien se registra desde la pantalla de registro de Breeze, no escoge rol.

Archivo:

```text
app/Http/Controllers/Auth/RegisteredUserController.php
```

Parte importante:

```php
$user = User::create([
    'name' => $request->name,
    'email' => $request->email,
    'password' => Hash::make($request->password),
    'role' => User::ROLE_CONSULTA,
]);
```

Que significa:

- Si un usuario se registra solo, se crea como `consulta`.
- Eso evita que un usuario nuevo tenga permisos de administrador o tecnico.
- Si luego se desea cambiar su rol, un administrador lo puede editar desde el modulo Usuarios.

## 8. Middleware de roles

El middleware es la parte que bloquea rutas segun el rol.

Archivo:

```text
app/Http/Middleware/RoleMiddleware.php
```

Codigo:

```php
public function handle(Request $request, Closure $next, string ...$roles): Response
{
    $user = $request->user();

    if (!$user) {
        return redirect()->route('login');
    }

    if (!in_array($user->role, $roles, true)) {
        abort(403, 'No autorizado.');
    }

    return $next($request);
}
```

Explicacion:

```php
$user = $request->user();
```

- Obtiene el usuario autenticado.

```php
if (!$user) {
    return redirect()->route('login');
}
```

- Si no hay usuario logueado, lo manda al login.

```php
if (!in_array($user->role, $roles, true)) {
    abort(403, 'No autorizado.');
}
```

- Compara el rol del usuario contra los roles permitidos en la ruta.
- Si no coincide, muestra error 403.

```php
return $next($request);
```

- Si el rol coincide, deja continuar la peticion al controlador.

## 9. Donde se registra el middleware role

Archivo:

```text
bootstrap/app.php
```

Parte importante:

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'role' => \App\Http\Middleware\RoleMiddleware::class,
    ]);
})
```

Que significa:

- Registra un alias llamado `role`.
- Gracias a eso se puede usar en rutas asi:

```php
->middleware('role:admin')
```

o asi:

```php
->middleware(['role:admin,tecnico'])
```

Sin ese alias, Laravel no sabria que clase ejecutar cuando se usa `role`.

## 10. Proteccion de rutas por rol

Archivo principal:

```text
routes/web.php
```

Todas las rutas importantes estan dentro de:

```php
Route::middleware(['auth', AuditMiddleware::class])->group(function () {
    ...
});
```

Esto significa:

- Primero el usuario debe iniciar sesion.
- Ademas, los cambios quedan registrados por auditoria.

Dentro de ese grupo se aplican roles.

### Dashboard

```php
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');
```

El dashboard esta dentro del grupo `auth`, por eso requiere login.
No tiene un `role:` especifico porque los 4 roles pueden consultarlo.

### Seguimiento y consulta

```php
Route::middleware(['role:' . implode(',', User::roleKeys())])->group(function () {
    ...
});
```

Que significa:

- `User::roleKeys()` devuelve todos los roles:

```text
admin, planificacion, tecnico, consulta
```

- `implode(',', ...)` los convierte en:

```text
admin,planificacion,tecnico,consulta
```

Entonces el middleware recibe todos los roles.
Esta zona permite consultar:

- seguimiento de metas
- organizacion
- programas
- proyectos
- trazabilidad
- reportes

### Registro de avances y evidencias

```php
Route::middleware(['role:admin,tecnico'])->group(function () {
    ...
});
```

Que significa:

- Solo entran `admin` y `tecnico`.
- Aqui estan las rutas para:
  - registrar avances de indicadores
  - editar avances de indicadores
  - eliminar avances de indicadores
  - registrar avances de proyectos
  - editar avances de proyectos
  - eliminar avances de proyectos
  - agregar evidencias
  - eliminar evidencias

### Planificacion

```php
Route::middleware(['role:admin,planificacion'])->group(function () {
    ...
});
```

Que significa:

- Solo entran `admin` y `planificacion`.
- Aqui estan los CRUD de:
  - entidades
  - programas
  - proyectos
  - objetivos estrategicos
  - ODS
  - PDN
  - planes
  - metas
  - indicadores
  - alineaciones

### Administracion y seguridad

```php
Route::middleware(['role:admin'])->group(function () {
    Route::resource('usuarios', UserController::class)->except(['show']);

    Route::get('/auditoria', [AuditLogController::class, 'index'])
        ->name('auditoria.index');
});
```

Que significa:

- Solo el administrador puede entrar.
- Admin gestiona usuarios.
- Admin consulta auditoria.

## 11. Rutas de prueba por rol

Al final de `routes/web.php` hay rutas simples:

```php
Route::get('/admin', fn () => 'Panel Admin OK')
    ->middleware(['auth', 'role:admin']);

Route::get('/tecnico', fn () => 'Panel Tecnico OK')
    ->middleware(['auth', 'role:tecnico']);

Route::get('/consulta', fn () => 'Panel Consulta OK')
    ->middleware(['auth', 'role:consulta']);

Route::get('/planificacion', fn () => 'Panel Planificacion OK')
    ->middleware(['auth', 'role:planificacion']);
```

Estas rutas sirven para probar rapido si un usuario tiene acceso a un rol especifico.
No son modulos completos, solo pruebas.

## 12. Roles en la barra lateral

Archivo:

```text
resources/views/layouts/navigation.blade.php
```

La barra lateral usa el usuario autenticado:

```blade
{{ auth()->user()->roleLabel() }}
```

Esto muestra el nombre bonito del rol.

### Menu visible para todos los roles autenticados

Estos enlaces aparecen para usuarios logueados:

- Dashboard
- Metas de seguimiento
- Organizacion
- Trazabilidad
- Reportes

Estan en la vista sin `@if` especial porque las rutas de consulta estan permitidas para los 4 roles.

### Menu de planificacion

Codigo:

```blade
@if(auth()->user()->canManagePlanning())
    ...
@endif
```

Que significa:

- Solo muestra ese bloque si el usuario puede gestionar planificacion.
- Segun el modelo `User`, eso ocurre si el rol es:
  - `admin`
  - `planificacion`

Dentro de ese bloque aparecen:

- Entidades
- Programas
- Proyectos
- Planes
- Metas CRUD
- Indicadores
- Alineaciones
- PND / PDN
- ODS
- Objetivos

### Menu de seguridad

Codigo:

```blade
@if(auth()->user()->isAdmin())
    ...
@endif
```

Que significa:

- Solo el administrador ve ese bloque.

Dentro aparecen:

- Usuarios
- Auditoria

Importante:

Ocultar enlaces en la vista mejora la experiencia, pero la seguridad real esta en `routes/web.php` con el middleware.
Aunque alguien escriba la URL manualmente, el middleware puede bloquearlo.

## 13. Roles en la vista de usuarios

Archivo:

```text
resources/views/usuarios/index.blade.php
```

Parte importante:

```blade
{{ $user->roleLabel() }}
```

Que hace:

- Muestra el rol con etiqueta bonita.
- No muestra `admin`, sino `Administrador del Sistema`.

Archivo:

```text
resources/views/usuarios/create.blade.php
```

Archivo:

```text
resources/views/usuarios/edit.blade.php
```

En ambos se usa:

```blade
@foreach(\App\Models\User::ROLE_LABELS as $role => $label)
```

Eso permite construir el select de roles desde el modelo.
Si un dia se cambia el nombre visible de un rol, se cambia en `User.php` y las vistas lo toman desde ahi.

## 14. Roles en vistas de seguimiento

### Boton registrar avance de indicador

Archivo:

```text
resources/views/seguimiento/meta_show.blade.php
```

Codigo:

```blade
@if(auth()->user()->canRegisterSeguimiento())
    <a class="btn" href="{{ route('indicadores.avance.create', $ind->id) }}">
        Registrar avance
    </a>
@endif
```

Que significa:

- El boton solo aparece si el usuario puede registrar seguimiento.
- Segun el modelo `User`, eso aplica para:
  - admin
  - tecnico

Autoridad/consulta no ve ese boton.
Planificacion tampoco lo ve, porque su rol es gestionar planificacion, no registrar seguimiento.

### Editar o eliminar avance de indicador

En la misma vista:

```blade
@if(auth()->id() === $last->user_id || auth()->user()->isAdmin())
    ...
@endif
```

Que significa:

- Puede editar/eliminar si el avance lo creo el usuario actual.
- O si el usuario es administrador.

Esto permite que un tecnico edite sus propios avances, pero no los de otro usuario.

### Boton registrar avance de proyecto

Archivo:

```text
resources/views/seguimiento/proyecto_show.blade.php
```

Codigo:

```blade
@if(auth()->user()->canRegisterSeguimiento())
    <a class="btn" href="{{ route('proyectos.avance.create', $proyecto->id) }}">
        + Registrar avance
    </a>
@endif
```

Misma idea:

- Admin y tecnico ven el boton.
- Planificacion y consulta no lo ven.

### Editar/eliminar avance de proyecto

Codigo:

```blade
@if(auth()->user()->isAdmin() || auth()->id() === $a->user_id)
    ...
@endif
```

Que significa:

- Admin puede editar/eliminar cualquier avance.
- El usuario que creo el avance puede editar/eliminar su propio avance.
- Otro tecnico no puede editar avances ajenos.

### Evidencias de proyecto

En `proyecto_show.blade.php` tambien aparece:

```blade
@if(auth()->user()->isAdmin() || auth()->id() === $a->user_id)
```

Esto controla:

- eliminar evidencia
- agregar nueva evidencia a un avance

## 15. Validaciones de propietario en controladores

Ocultar botones en vistas no es suficiente.
Tambien se valida en controladores.

### Avances de indicadores

Archivo:

```text
app/Http/Controllers/AvanceIndicadorController.php
```

Ejemplo:

```php
if ($avance->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
    abort(403);
}
```

Que significa:

- Si el avance no pertenece al usuario actual,
- y el usuario no es admin,
- entonces se bloquea con error 403.

Este mismo control aparece en:

- `edit`
- `update`
- `destroy`

Por eso, aunque alguien intente entrar manualmente a una URL de editar avance, el controlador vuelve a revisar.

### Avances de proyectos

Archivo:

```text
app/Http/Controllers/ProyectoAvanceController.php
```

Se usa la misma regla:

```php
if ($avance->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
    abort(403);
}
```

Se aplica en:

- editar avance
- actualizar avance
- agregar evidencia
- eliminar evidencia
- eliminar avance completo

Esto refuerza la regla funcional:

> El tecnico solo edita o elimina sus propios avances.

## 16. Relacion entre rutas, vistas y controladores

Hay tres niveles de proteccion:

### Nivel 1: Rutas

Archivo:

```text
routes/web.php
```

Aqui se bloquea el acceso a modulos completos.

Ejemplo:

```php
Route::middleware(['role:admin'])->group(function () {
    Route::resource('usuarios', UserController::class)->except(['show']);
});
```

Esto evita que alguien que no sea admin entre al modulo usuarios.

### Nivel 2: Vistas

Archivos:

```text
resources/views/layouts/navigation.blade.php
resources/views/seguimiento/meta_show.blade.php
resources/views/seguimiento/proyecto_show.blade.php
```

Aqui se ocultan botones o menus segun el rol.

Ejemplo:

```blade
@if(auth()->user()->canRegisterSeguimiento())
```

Esto evita mostrar botones que no corresponden.

### Nivel 3: Controladores

Archivos:

```text
app/Http/Controllers/AvanceIndicadorController.php
app/Http/Controllers/ProyectoAvanceController.php
```

Aqui se bloquea una accion especifica si el usuario no es dueño del registro o admin.

Ejemplo:

```php
if ($avance->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
    abort(403);
}
```

Esto es importante porque un usuario podria intentar escribir la URL manualmente.
El controlador protege aunque el boton no aparezca.

## 17. Que puede hacer cada rol

### Administrador del Sistema (`admin`)

Puede:

- Ver dashboard.
- Consultar seguimiento.
- Consultar trazabilidad.
- Consultar reportes.
- Gestionar entidades.
- Gestionar programas.
- Gestionar proyectos.
- Gestionar planes.
- Gestionar metas.
- Gestionar indicadores.
- Gestionar ODS, PDN y objetivos.
- Gestionar alineaciones.
- Registrar avances.
- Editar/eliminar cualquier avance.
- Gestionar usuarios.
- Ver auditoria.

En codigo se identifica con:

```php
User::ROLE_ADMIN
```

o:

```php
auth()->user()->isAdmin()
```

### Responsable de Planificacion (`planificacion`)

Puede:

- Ver dashboard.
- Consultar seguimiento.
- Consultar trazabilidad.
- Consultar reportes.
- Gestionar entidades.
- Gestionar programas.
- Gestionar proyectos.
- Gestionar planes.
- Gestionar metas.
- Gestionar indicadores.
- Gestionar ODS, PDN y objetivos.
- Gestionar alineaciones.

No puede:

- Gestionar usuarios.
- Ver auditoria.
- Registrar avances de seguimiento.

En codigo se permite con:

```php
auth()->user()->canManagePlanning()
```

o en rutas:

```php
role:admin,planificacion
```

### Tecnico de Seguimiento (`tecnico`)

Puede:

- Ver dashboard.
- Consultar seguimiento.
- Consultar trazabilidad.
- Consultar reportes.
- Registrar avances de indicadores.
- Registrar avances de proyectos.
- Subir evidencias.
- Editar/eliminar sus propios avances.

No puede:

- Gestionar usuarios.
- Ver auditoria.
- Gestionar catalogos de planificacion.
- Editar avances de otros usuarios, salvo que sea admin.

En codigo se permite con:

```php
auth()->user()->canRegisterSeguimiento()
```

o en rutas:

```php
role:admin,tecnico
```

### Autoridad / Consulta (`consulta`)

Puede:

- Ver dashboard.
- Consultar seguimiento.
- Consultar trazabilidad.
- Consultar reportes.

No puede:

- Crear.
- Editar.
- Eliminar.
- Registrar avances.
- Gestionar usuarios.
- Ver auditoria.

Es el rol por defecto para nuevos usuarios.

## 18. Ejemplo completo: usuario tecnico registra avance

1. El usuario tecnico inicia sesion.
2. En la barra lateral ve seguimiento.
3. Entra a una meta o proyecto.
4. La vista revisa:

```blade
auth()->user()->canRegisterSeguimiento()
```

5. Como es tecnico, el boton `Registrar avance` aparece.
6. El usuario registra el avance.
7. El controlador guarda el avance con:

```php
'user_id' => Auth::id()
```

8. Ese `user_id` sirve despues para saber quien es dueño del avance.
9. Si intenta editarlo, el controlador valida:

```php
$avance->user_id !== Auth::id() && !Auth::user()->isAdmin()
```

10. Si es su avance, puede editarlo.
11. Si no es suyo, se bloquea con 403.

## 19. Ejemplo completo: usuario consulta intenta entrar a usuarios

1. El usuario con rol `consulta` inicia sesion.
2. En la barra lateral no ve el menu Seguridad.
3. Si escribe manualmente `/usuarios`, Laravel pasa por `routes/web.php`.
4. Esa ruta esta dentro de:

```php
Route::middleware(['role:admin'])->group(...)
```

5. `RoleMiddleware` compara:

```php
$user->role
```

contra:

```text
admin
```

6. Como `consulta` no coincide, Laravel ejecuta:

```php
abort(403, 'No autorizado.');
```

7. El acceso queda bloqueado.

## 20. Como explicarlo al profesor en pocas palabras

El proyecto maneja roles con un campo `role` en la tabla `users`.
Los roles estan definidos como constantes en el modelo `User`, para no escribir textos sueltos por todo el sistema.
El administrador puede asignar o cambiar roles desde el modulo Usuarios.
El formulario de usuarios toma las opciones desde `User::ROLE_LABELS`, y el controlador valida que el rol enviado exista usando `Rule::in(User::roleKeys())`.

La seguridad principal esta en las rutas, usando un middleware propio llamado `RoleMiddleware`.
Ese middleware esta registrado con el alias `role` en `bootstrap/app.php`.
Por eso en `routes/web.php` se puede escribir `role:admin`, `role:admin,tecnico` o `role:admin,planificacion`.
Si el rol del usuario no esta permitido, se devuelve error 403.

Ademas, las vistas usan metodos del modelo `User` para mostrar u ocultar opciones.
Por ejemplo, `canManagePlanning()` muestra modulos de planificacion solo a admin y planificacion.
`canRegisterSeguimiento()` muestra botones de registrar avances solo a admin y tecnico.
`isAdmin()` muestra opciones de seguridad solo al administrador.

Finalmente, en los controladores de avances existe una validacion extra de propietario.
Eso permite que un tecnico edite o elimine solo los avances que el mismo registro, mientras que el administrador puede gestionar todos.

