<?php

use Illuminate\Foundation\Application;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
   ->withExceptions(function (Exceptions $exceptions) {
       // Exception handling configuration
       $exceptions->render(function (AuthorizationException $e) {
           return response()->json([
               'errors' => [
                   'errorDetails' => $e->getMessage(),
               ]
           ], $e->status ?? 403);
       });

       $exceptions->render(function (ModelNotFoundException $e) {
           return response()->json([
               'errors' => [
                   'errorDetails' => __("السجل غيرموجود "),
                   'status' => 'eror',
               ]
           ], 404);
       });

       $exceptions->render(function (NotFoundHttpException $e) {
           return response()->json([
                'errors' => [
                    'errorDetails' => __("السجل غيرموجود "),
                    'status' => 'eror',
                ]
            ], 404);
       });
   })
    ->create();
