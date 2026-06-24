<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // Listar usuarios.
    public function index()
    {
        $usuarios = User::orderBy('id', 'desc')->paginate(10);

        return view('usuarios.index', compact('usuarios'));
    }

    // Mostrar formulario para crear usuario.
    public function create()
    {
        return view('usuarios.create');
    }

    // Guardar usuario.
    public function store(Request $request)
    {
        // Validacion de usuario y rol.
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in(User::roleKeys())],
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'activo' => true,
        ]);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    // Mostrar formulario para editar usuario.
    public function edit(User $usuario)
    {
        return view('usuarios.edit', compact('usuario'));
    }

    // Actualizar usuario.
    public function update(Request $request, User $usuario)
    {
        // Validacion de usuario y rol.
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($usuario->id)],
            'role' => ['required', Rule::in(User::roleKeys())],
            'password' => 'nullable|string|min:8|confirmed',
            'activo' => ['nullable', 'boolean'],
        ]);

        $usuario->name = $data['name'];
        $usuario->email = $data['email'];
        $usuario->role = $data['role'];
        $usuario->activo = $request->boolean('activo');

        // Actualizacion opcional de clave.
        if (! empty($data['password'])) {
            $usuario->password = Hash::make($data['password']);
        }

        $usuario->save();

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    // Desactivar usuario.
    public function destroy(User $usuario)
    {
        // Proteccion de la cuenta actual.
        if (auth()->id() === $usuario->id) {
            return redirect()->route('usuarios.index')
                ->with('success', 'No puedes eliminar tu propio usuario.');
        }

        // Desactivacion para conservar auditoria.
        $usuario->update(['activo' => false]);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario desactivado correctamente.');
    }
}
