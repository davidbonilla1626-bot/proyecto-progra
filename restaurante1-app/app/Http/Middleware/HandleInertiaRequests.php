<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $openingTime = \App\Models\Setting::getVal('opening_time', '08:00');
        $closingTime = \App\Models\Setting::getVal('closing_time', '22:00');
        $currentTime = now()->format('H:i');
        $isOpen = ($currentTime >= $openingTime && $currentTime <= $closingTime);

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'role' => $request->user()->role,
                    'points' => $request->user()->points,
                ] : null,
            ],
            'shop' => [
                'opening_time' => $openingTime,
                'closing_time' => $closingTime,
                'is_open' => $isOpen,
            ]
        ];
    }
}
