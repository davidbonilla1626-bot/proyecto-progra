<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Users/Index', [
            'users' => User::orderBy('name', 'asc')->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|in:user,employee,admin',
        ], [
            'email.unique' => 'Este correo ya está registrado.',
            'role.in' => 'El rol seleccionado no es válido.'
        ]);

        $newUser = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        \App\Models\AuditLog::log("Creó el usuario administrativamente: {$newUser->name} ({$newUser->email}) con rol: {$newUser->role}");

        return redirect()->route('users.index')->with('message', 'Usuario creado correctamente.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:user,employee,admin',
            'password' => 'nullable|string|min:8',
        ], [
            'email.unique' => 'Este correo ya está registrado.',
            'role.in' => 'El rol seleccionado no es válido.'
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($validated['password']);
        }

        $oldRole = $user->role;
        $user->update($data);

        if ($oldRole !== $validated['role']) {
            \App\Models\AuditLog::log("Cambió el rol del usuario {$user->name} de '{$oldRole}' a '{$validated['role']}'");
        } else {
            \App\Models\AuditLog::log("Actualizó datos del usuario: {$user->name} ({$user->email})");
        }

        return redirect()->route('users.index')->with('message', 'Usuario actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // No permitir que un admin se elimine a sí mismo
        if (auth()->id() === $user->id) {
            return redirect()->back()->with('error', 'No puedes eliminar tu propio usuario.');
        }

        $userName = $user->name;
        $userEmail = $user->email;
        $userId = $user->id;
        $user->delete();

        \App\Models\AuditLog::log("Eliminó al usuario ID {$userId}: {$userName} ({$userEmail})");

        return redirect()->route('users.index')->with('message', 'Usuario eliminado correctamente.');
    }
}
