<?php

use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Preview berjalan di belakang reverse proxy (Vite dev server / ingress):
        // percayai X-Forwarded-* agar URL & cookie seskema https.
        $middleware->trustProxies(at: '*');
        $middleware->redirectUsersTo(fn (Request $request) => match ($request->user()?->role) {
            User::ROLE_ADMIN => route('admin.dashboard'),
            User::ROLE_CHEF => route('chef.dashboard'),
            default => route('learner.dashboard'),
        });

        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
