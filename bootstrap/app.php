<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // 405 - Method tidak sesuai 
        $exceptions->render(function (MethodNotAllowedHttpException $e, $request) {
            if (!Auth::check()) {
                return redirect()->route('login');
            }
        });

        // 404 - URL tidak ditemukan / diisengi
        $exceptions->render(function (NotFoundHttpException $e, $request) {
            if (!Auth::check()) {
                return redirect()->route('login');
            }
        });
    })->create();
