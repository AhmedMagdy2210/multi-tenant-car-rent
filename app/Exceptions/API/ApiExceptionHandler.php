<?php

namespace App\Exceptions\API;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ApiExceptionHandler extends Exception
{
    public static array $handlers = [
        AuthenticationException::class => 'handelAuthenticationException',
        AccessDeniedException::class => 'handelAuthenticationException',
        AuthorizationException::class => 'handleAuthorizationException',
        ValidationException::class => 'handleValidationException',
        ModelNotFoundException::class => 'handleNotFoundException',
        NotFoundHttpException::class => 'handleNotFoundException',
        MethodNotAllowedHttpException::class => 'handleMethodNotAllowedHttpException',
        HttpException::class => 'handleHttpException',
    ];

    public function handelAuthenticationException(AuthenticationException|AccessDeniedException $e, Request $request)
    {
        return error('Unauthenticated.', 401);
    }
    public function handleAuthorizationException(AuthorizationException $e, Request $request)
    {
        return error('Unautorized.', 403);
    }
    public function handleValidationException(ValidationException $e, Request $request)
    {
        return error('Validation failed.', 422, $e->errors());
    }
    public function handleNotFoundException(ModelNotFoundException|NotFoundHttpException $e, Request $request)
    {
        $message = str_contains($e->getMessage(), 'No query results for model')
            ? 'Resource not found.'
            : 'Route not found.';
        return error($message, 404);
    }
    public function handleMethodNotAllowedHttpException(MethodNotAllowedHttpException $e, Request $request)
    {
        return error('Method not allowed.', 405);
    }
    public function handleHttpException(HttpException $e, Request $request)
    {
        return error($e->getMessage() ?: 'An Http error occurred.', $e->getStatusCode());
    }
}
