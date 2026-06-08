<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed'],
            'admin_key' => 'nullable|string',
        ]);

        $role = 'user';

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $role,
        ]);

        event(new Registered($user));

        // Registrar en Auditoría
        \App\Models\AuditLog::create([
            'user_id' => $user->id,
            'action' => "Se registró un nuevo usuario: {$user->name} ({$user->email})",
            'ip_address' => $request->ip()
        ]);

        // Enviar Correo de Bienvenida
        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\QuickBiteMail(
                '¡Bienvenido a QuickBite Express!',
                $user->name,
                "Tu cuenta ha sido creada exitosamente.\n\nYa puedes explorar nuestro menú de comida rápida brutalista, acumular puntos de fidelidad en cada compra y canjearlos por deliciosas recompensas gratis.",
                url('/menu'),
                'Ir al Menú'
            ));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Error enviando correo de registro: " . $e->getMessage());
        }

        Auth::login($user);

        if ($user->isAdmin()) {
            return redirect()->route('dashboard');
        }
        return redirect()->route('public.menu');
    }
}
