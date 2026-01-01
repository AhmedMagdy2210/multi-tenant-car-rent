<?php

use App\Exceptions\API\ApiExceptionHandler;
use App\Http\Middleware\System\UserRoleMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => UserRoleMiddleware::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e, Request $request) {
            $className = get_class($e);
            $handlers = ApiExceptionHandler::$handlers;
            if (array_key_exists($className, $handlers)) {
                $method = $handlers[$className];
                $apiHandler = new ApiExceptionHandler();
                return $apiHandler->$method($e, $request);
            }
            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'An unexpected error occurred.',
                'code' => $e->getCode()
            ], 500);
        });
    })->create();
