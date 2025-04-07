<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, $exception)
    {
        // Manejo específico para errores de autenticación JWT
        if ($exception instanceof AuthenticationException) {
            return response()->json([
                'status' => 'error',
                'message' => 'No autenticado',
                'error' => 'Token JWT inválido, expirado o no proporcionado',
                'solution' => 'Por favor inicie sesión nuevamente'
            ], 401);
        }

        // Manejo de errores de validación
        if ($exception instanceof ValidationException) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error de validación',
                'errors' => $exception->errors(),
            ], 422);
        }

        // Manejo de rutas no encontradas
        if ($exception instanceof NotFoundHttpException) {
            return response()->json([
                'status' => 'error',
                'message' => 'Endpoint no encontrado',
                'error' => 'La URL solicitada no existe'
            ], 404);
        }

        // Manejo de métodos no permitidos
        if ($exception instanceof MethodNotAllowedHttpException) {
            return response()->json([
                'status' => 'error',
                'message' => 'Método no permitido',
                'error' => 'El método HTTP utilizado no está soportado para este endpoint'
            ], 405);
        }

        // Manejo genérico para otros errores
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error interno del servidor',
                'error' => env('APP_DEBUG') ? $exception->getMessage() : 'Ocurrió un error inesperado',
                'debug' => env('APP_DEBUG') ? [
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'trace' => $exception->getTrace()
                ] : null
            ], 500);
        }

        return parent::render($request, $exception);
    }
}