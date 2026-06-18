# Manual del proceso de login y recuperacion de clave

Este manual explica como funciona el inicio de sesion y la recuperacion de clave en este proyecto Laravel.
La idea es poder estudiarlo y explicarlo con palabras simples, entendiendo que archivo participa en cada paso.

## 1. Tecnologias usadas

El proceso usa estas tecnologias:

- **Laravel**: framework principal del proyecto.
- **PHP**: lenguaje usado en controladores, modelos, rutas y validaciones del servidor.
- **Blade**: motor de vistas de Laravel; se usa en las pantallas de login y recuperacion.
- **MySQL**: base de datos donde estan los usuarios.
- **Laravel Breeze**: paquete base que genero la autenticacion inicial.
- **Mailpit**: herramienta local que recibe correos de prueba, como el correo de recuperacion de clave.
- **HTML y CSS**: estructura y estilos de las vistas.
- **JavaScript / Alpine.js**: se usa en partes del layout, aunque el login principal no depende mucho de JS.

## 2. Archivos principales del proceso

### Rutas

Archivo:

```text
routes/auth.php
```

Aqui estan las rutas de autenticacion:

- `GET /login`: muestra la pantalla de login.
- `POST /login`: procesa el login.
- `POST /logout`: cierra la sesion.
- `GET /forgot-password`: muestra el formulario para pedir recuperacion de clave.
- `POST /forgot-password`: envia el correo con el enlace de recuperacion.
- `GET /reset-password/{token}`: muestra el formulario para escribir la nueva clave.
- `POST /reset-password`: guarda la nueva clave.

Este archivo se carga desde:

```text
routes/web.php
```

Al final de `routes/web.php` aparece:

```php
require __DIR__.'/auth.php';
```

Eso significa que Laravel incluye todas las rutas de autenticacion definidas por Breeze.

## 3. Proceso de login

### Paso 1: El usuario entra a la pantalla de login

Ruta:

```php
Route::get('login', [AuthenticatedSessionController::class, 'create'])
    ->name('login');
```

Archivo:

```text
app/Http/Controllers/Auth/AuthenticatedSessionController.php
```

Metodo:

```php
public function create(): View
{
    return view('auth.login');
}
```

Que hace:

- Cuando el usuario entra a `/login`, Laravel llama al metodo `create`.
- Ese metodo devuelve la vista `auth.login`.
- La vista real esta en:

```text
resources/views/auth/login.blade.php
```

### Paso 2: La vista muestra el formulario

Archivo:

```text
resources/views/auth/login.blade.php
```

Partes importantes:

```blade
<form method="POST" action="{{ route('login') }}">
    @csrf
```

Esto significa:

- El formulario envia datos por metodo `POST`.
- La ruta destino es `login`.
- `@csrf` agrega un token de seguridad para evitar envios falsos desde otro sitio.

Campos principales:

```blade
<x-text-input id="email" type="email" name="email" required />
<x-text-input id="password" type="password" name="password" required />
```

Esto significa:

- El usuario escribe su correo en `email`.
- El usuario escribe su clave en `password`.
- Esos nombres son importantes porque Laravel los lee despues en el request.

Tambien esta el checkbox:

```blade
<input id="remember_me" type="checkbox" name="remember">
```

Esto sirve para recordar la sesion si el usuario marca la opcion.

### Paso 3: El formulario se envia al controlador

Ruta:

```php
Route::post('login', [AuthenticatedSessionController::class, 'store']);
```

Archivo:

```text
app/Http/Controllers/Auth/AuthenticatedSessionController.php
```

Metodo:

```php
public function store(LoginRequest $request): RedirectResponse
{
    $request->authenticate();

    $request->session()->regenerate();

    return redirect()->intended(route('dashboard', absolute: false));
}
```

Que hace cada parte:

```php
$request->authenticate();
```

- Llama a la validacion y autenticacion.
- Este metodo no esta en el controlador, esta en `LoginRequest`.

```php
$request->session()->regenerate();
```

- Regenera la sesion del usuario.
- Esto ayuda a proteger el login contra ataques de fijacion de sesion.

```php
return redirect()->intended(route('dashboard', absolute: false));
```

- Si el login fue correcto, manda al usuario al dashboard.
- Si antes intento entrar a otra pagina protegida, Laravel puede redirigirlo a esa pagina.

## 4. Validaciones del login

Archivo:

```text
app/Http/Requests/Auth/LoginRequest.php
```

Este archivo es muy importante porque separa la validacion del login del controlador.

### Metodo `rules`

```php
public function rules(): array
{
    return [
        'email' => ['required', 'string', 'email'],
        'password' => ['required', 'string'],
    ];
}
```

Que valida:

- `email` es obligatorio.
- `email` debe ser texto.
- `email` debe tener formato de correo.
- `password` es obligatorio.
- `password` debe ser texto.

Si algo falla, Laravel regresa a la vista y muestra el error con:

```blade
<x-input-error :messages="$errors->get('email')" class="mt-2" />
<x-input-error :messages="$errors->get('password')" class="mt-2" />
```

Esos componentes estan en:

```text
resources/views/components/input-error.blade.php
```

### Metodo `authenticate`

```php
public function authenticate(): void
{
    $this->ensureIsNotRateLimited();

    if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
        RateLimiter::hit($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.failed'),
        ]);
    }

    RateLimiter::clear($this->throttleKey());
}
```

Explicacion:

```php
$this->ensureIsNotRateLimited();
```

- Revisa que el usuario no haya fallado demasiadas veces.
- En este proyecto Laravel permite 5 intentos antes de bloquear temporalmente.

```php
Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))
```

- Laravel intenta iniciar sesion con el correo y clave.
- Busca el usuario en la tabla `users`.
- Compara la clave ingresada con la clave hasheada guardada en base de datos.
- Si `remember` esta marcado, recuerda la sesion.

```php
RateLimiter::hit($this->throttleKey());
```

- Si falla, suma un intento fallido.

```php
throw ValidationException::withMessages([
    'email' => trans('auth.failed'),
]);
```

- Si las credenciales no coinciden, genera un error.
- El texto sale de:

```text
lang/es/auth.php
```

En ese archivo esta:

```php
'failed' => 'Estas credenciales no coinciden con nuestros registros.',
```

### Metodo `ensureIsNotRateLimited`

```php
if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
    return;
}
```

Esto revisa si el usuario ya fallo 5 veces.

Si fallo demasiado, Laravel muestra un mensaje como:

```text
Demasiados intentos. Intenta nuevamente en X segundos.
```

Ese texto tambien esta en:

```text
lang/es/auth.php
```

## 5. De donde salen los usuarios

Modelo:

```text
app/Models/User.php
```

Laravel usa este modelo porque en `config/auth.php` esta configurado:

```php
'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => env('AUTH_MODEL', App\Models\User::class),
    ],
],
```

Eso significa:

- Laravel usa Eloquent.
- El modelo principal de usuarios es `App\Models\User`.
- Ese modelo representa la tabla `users`.

En tu proyecto, `User.php` tambien tiene roles:

```php
public const ROLE_ADMIN = 'admin';
public const ROLE_PLANIFICACION = 'planificacion';
public const ROLE_TECNICO = 'tecnico';
public const ROLE_CONSULTA = 'consulta';
```

El login solo verifica correo y clave.
Luego, las rutas protegidas revisan el rol usando el middleware:

```text
app/Http/Middleware/RoleMiddleware.php
```

## 6. Cierre de sesion

Ruta:

```php
Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');
```

Controlador:

```text
app/Http/Controllers/Auth/AuthenticatedSessionController.php
```

Metodo:

```php
public function destroy(Request $request): RedirectResponse
{
    Auth::guard('web')->logout();

    $request->session()->invalidate();

    $request->session()->regenerateToken();

    return redirect('/');
}
```

Explicacion:

```php
Auth::guard('web')->logout();
```

- Cierra la sesion del usuario.

```php
$request->session()->invalidate();
```

- Borra la sesion actual.

```php
$request->session()->regenerateToken();
```

- Genera un nuevo token CSRF.

```php
return redirect('/');
```

- Manda al usuario a la landing page.

## 7. Recuperacion de clave

### Paso 1: El usuario entra a "Olvidaste tu clave?"

En la vista:

```text
resources/views/auth/login.blade.php
```

Existe este enlace:

```blade
<a href="{{ route('password.request') }}">
    Olvidaste tu clave?
</a>
```

Ese enlace usa esta ruta:

```php
Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
    ->name('password.request');
```

Controlador:

```text
app/Http/Controllers/Auth/PasswordResetLinkController.php
```

Metodo:

```php
public function create(): View
{
    return view('auth.forgot-password');
}
```

Esto muestra la vista:

```text
resources/views/auth/forgot-password.blade.php
```

### Paso 2: El usuario escribe su correo

Vista:

```text
resources/views/auth/forgot-password.blade.php
```

Formulario:

```blade
<form method="POST" action="{{ route('password.email') }}">
    @csrf
```

Campo:

```blade
<x-text-input id="email" type="email" name="email" required autofocus />
```

Cuando el usuario presiona el boton, se ejecuta esta ruta:

```php
Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
    ->name('password.email');
```

### Paso 3: Laravel valida el correo y envia el enlace

Controlador:

```text
app/Http/Controllers/Auth/PasswordResetLinkController.php
```

Metodo:

```php
public function store(Request $request): RedirectResponse
{
    $request->validate([
        'email' => ['required', 'email'],
    ]);

    $status = Password::sendResetLink(
        $request->only('email')
    );

    return $status == Password::RESET_LINK_SENT
                ? back()->with('status', __($status))
                : back()->withInput($request->only('email'))
                    ->withErrors(['email' => __($status)]);
}
```

Explicacion:

```php
$request->validate([
    'email' => ['required', 'email'],
]);
```

- Revisa que el correo sea obligatorio.
- Revisa que tenga formato de correo.

```php
Password::sendResetLink($request->only('email'));
```

Esta es la parte clave.

Laravel hace internamente esto:

1. Busca un usuario con ese correo en la tabla `users`.
2. Si existe, genera un token de recuperacion.
3. Guarda ese token en la tabla `password_reset_tokens`.
4. Genera un enlace parecido a:

```text
http://localhost/reset-password/TOKEN?email=correo@ejemplo.com
```

5. Envia un correo al usuario.

El comportamiento del token esta configurado en:

```text
config/auth.php
```

Parte importante:

```php
'passwords' => [
    'users' => [
        'provider' => 'users',
        'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
        'expire' => 60,
        'throttle' => 60,
    ],
],
```

Esto significa:

- Usa el provider `users`.
- Guarda tokens en `password_reset_tokens`.
- El enlace dura 60 minutos.
- El usuario debe esperar 60 segundos antes de pedir otro enlace.

## 8. Conexion con Mailpit

La conexion con Mailpit no esta en un controlador.
Esta en la configuracion de correo.

Archivo:

```text
.env
```

Valores importantes:

```env
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_FROM_ADDRESS="sipeip@academico.test"
MAIL_FROM_NAME="${APP_NAME}"
```

Explicacion:

- `MAIL_MAILER=smtp`: Laravel envia correos usando SMTP.
- `MAIL_HOST=127.0.0.1`: el servidor de correo esta en la misma maquina.
- `MAIL_PORT=1025`: Mailpit recibe correos SMTP en ese puerto.
- `MAIL_FROM_ADDRESS`: correo remitente de prueba.
- `MAIL_FROM_NAME`: nombre que aparece como remitente.

Archivo:

```text
config/mail.php
```

Parte importante:

```php
'default' => env('MAIL_MAILER', 'log'),
```

Laravel toma el mailer desde `.env`.

Y en la configuracion SMTP:

```php
'smtp' => [
    'transport' => 'smtp',
    'host' => env('MAIL_HOST', '127.0.0.1'),
    'port' => env('MAIL_PORT', 2525),
    'username' => env('MAIL_USERNAME'),
    'password' => env('MAIL_PASSWORD'),
],
```

Cuando se ejecuta:

```php
Password::sendResetLink(...)
```

Laravel usa esta configuracion para enviar el correo.
Mailpit lo captura y se puede ver en:

```text
http://localhost:8025
```

## 9. Donde se genera el contenido del correo

En tu proyecto no hay un archivo Blade propio para ese correo.
Laravel genera el correo con una notificacion interna.

La clase base de Laravel se llama:

```text
Illuminate\Auth\Notifications\ResetPassword
```

Esa clase esta dentro de `vendor/`, o sea, codigo del framework.
No conviene editar `vendor/` porque se puede perder al actualizar dependencias.

El correo se genera internamente cuando se llama:

```php
Password::sendResetLink($request->only('email'));
```

El contenido del correo usa traducciones como:

```text
Reset Password Notification
You are receiving this email because...
Reset Password
This password reset link will expire...
```

En este proyecto esas frases fueron traducidas en:

```text
lang/es.json
```

Ejemplos:

```json
"Reset Password Notification": "Restablecimiento de clave",
"You are receiving this email because we received a password reset request for your account.": "Recibes este correo porque solicitaste restablecer la clave de tu cuenta.",
"Reset Password": "Restablecer clave"
```

Por eso el correo que llega a Mailpit sale en español.

## 10. Formulario para crear nueva clave

Cuando el usuario abre el enlace del correo, entra a esta ruta:

```php
Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
    ->name('password.reset');
```

Controlador:

```text
app/Http/Controllers/Auth/NewPasswordController.php
```

Metodo:

```php
public function create(Request $request): View
{
    return view('auth.reset-password', ['request' => $request]);
}
```

Vista:

```text
resources/views/auth/reset-password.blade.php
```

Esa vista tiene:

```blade
<input type="hidden" name="token" value="{{ $request->route('token') }}">
```

Esto guarda el token que venia en la URL.
El usuario no lo ve, pero se manda junto con el formulario.

Campos:

- correo electronico
- nueva clave
- confirmar clave

## 11. Guardar la nueva clave

Ruta:

```php
Route::post('reset-password', [NewPasswordController::class, 'store'])
    ->name('password.store');
```

Controlador:

```text
app/Http/Controllers/Auth/NewPasswordController.php
```

Metodo:

```php
public function store(Request $request): RedirectResponse
{
    $request->validate([
        'token' => ['required'],
        'email' => ['required', 'email'],
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function (User $user) use ($request) {
            $user->forceFill([
                'password' => Hash::make($request->password),
                'remember_token' => Str::random(60),
            ])->save();

            event(new PasswordReset($user));
        }
    );

    return $status == Password::PASSWORD_RESET
                ? redirect()->route('login')->with('status', __($status))
                : back()->withInput($request->only('email'))
                    ->withErrors(['email' => __($status)]);
}
```

Explicacion de validaciones:

```php
'token' => ['required'],
```

- El token del correo es obligatorio.

```php
'email' => ['required', 'email'],
```

- El correo es obligatorio y debe tener formato valido.

```php
'password' => ['required', 'confirmed', Rules\Password::defaults()],
```

- La nueva clave es obligatoria.
- `confirmed` exige que exista `password_confirmation` y que coincida.
- `Rules\Password::defaults()` aplica la regla por defecto de Laravel para clave.

Luego:

```php
Password::reset(...)
```

Laravel revisa:

- Que el correo exista.
- Que el token sea valido.
- Que el token no haya expirado.

Si todo esta bien, ejecuta esta funcion:

```php
function (User $user) use ($request) {
    $user->forceFill([
        'password' => Hash::make($request->password),
        'remember_token' => Str::random(60),
    ])->save();

    event(new PasswordReset($user));
}
```

Que hace:

```php
Hash::make($request->password)
```

- Encripta/hashea la nueva clave.
- Nunca se guarda la clave en texto plano.

```php
'remember_token' => Str::random(60)
```

- Genera un nuevo token de recordar sesion.

```php
event(new PasswordReset($user));
```

- Lanza un evento interno indicando que la clave fue cambiada.

## 12. Traducciones y mensajes en español

Los textos de validacion estan en:

```text
lang/es/validation.php
```

Ejemplo:

```php
'required' => 'El campo :attribute es obligatorio.',
'email' => 'El campo :attribute debe ser un correo electronico valido.',
'min' => [
    'string' => 'El campo :attribute debe tener al menos :min caracteres.',
],
```

Los textos de login fallido estan en:

```text
lang/es/auth.php
```

Los textos de recuperacion de clave estan en:

```text
lang/es/passwords.php
```

Los textos del correo generado por Laravel estan en:

```text
lang/es.json
```

La configuracion del idioma esta en:

```text
.env
```

Con:

```env
APP_LOCALE=es
APP_FALLBACK_LOCALE=es
APP_FAKER_LOCALE=es_ES
```

## 13. Resumen completo del login

Flujo:

1. El usuario entra a `/login`.
2. `routes/auth.php` llama a `AuthenticatedSessionController@create`.
3. El controlador muestra `resources/views/auth/login.blade.php`.
4. El usuario escribe correo y clave.
5. El formulario envia `POST /login`.
6. Laravel llama a `AuthenticatedSessionController@store`.
7. El metodo `store` usa `LoginRequest`.
8. `LoginRequest` valida los campos.
9. `Auth::attempt` busca el usuario y compara la clave.
10. Si esta correcto, Laravel regenera la sesion.
11. El usuario entra al dashboard.
12. Si falla, vuelve al login con mensaje de error en español.

## 14. Resumen completo de recuperacion de clave

Flujo:

1. El usuario entra a `/forgot-password`.
2. Laravel muestra `auth.forgot-password`.
3. El usuario escribe su correo.
4. El formulario envia `POST /forgot-password`.
5. `PasswordResetLinkController@store` valida el correo.
6. `Password::sendResetLink` busca el usuario.
7. Laravel genera un token.
8. Laravel guarda el token en `password_reset_tokens`.
9. Laravel genera el correo de recuperacion.
10. Laravel usa `config/mail.php` y `.env` para enviarlo por SMTP.
11. Mailpit captura el correo en `http://localhost:8025`.
12. El usuario abre el enlace.
13. Laravel muestra `auth.reset-password`.
14. El usuario escribe nueva clave y confirmacion.
15. `NewPasswordController@store` valida token, correo y clave.
16. Si todo esta correcto, Laravel guarda la nueva clave hasheada.
17. El usuario vuelve al login.

## 15. Como explicarlo al profesor en pocas palabras

El proyecto usa Laravel Breeze como base de autenticacion.
El login esta dividido en rutas, vistas, controlador y request de validacion.
La vista solo muestra el formulario.
El controlador recibe la peticion.
La clase `LoginRequest` valida los datos y llama a `Auth::attempt` para comprobar correo y clave contra la tabla `users`.
Si el login es correcto, se regenera la sesion y el usuario entra al dashboard.

La recuperacion de clave usa el sistema interno de Laravel.
El usuario escribe su correo, Laravel genera un token temporal, lo guarda en la tabla `password_reset_tokens` y envia un correo con un enlace.
Ese correo se envia mediante SMTP usando Mailpit en entorno local.
El contenido del correo lo genera Laravel con una notificacion interna, y los textos se traducen desde `lang/es.json`.
Cuando el usuario abre el enlace, escribe una nueva clave; Laravel valida el token y guarda la nueva clave hasheada en la tabla `users`.

