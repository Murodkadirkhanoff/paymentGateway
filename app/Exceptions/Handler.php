<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Throwable;

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

    public function render($request, Throwable $exception)
    {
        return $this->handleException($request, $exception);
    }

    public function handleException($request, Throwable $exception)
    {

        if ($exception instanceof MethodNotAllowedHttpException) {
            return $this->errorResponse([
                'key' => 'not_found',
                'message' => 'Указанный метод для запроса недействителен'
            ], 405);
        }

        if ($exception instanceof RouteNotFoundException || $exception instanceof NotFoundHttpException) {
            return $this->errorResponse([[
                'key' => 'not_found',
                'message' => 'Указанная ссылка не найдена'
            ]], 404, 1);
        }

        if ($exception instanceof ModelNotFoundException || $exception->getCode() == 404) {
            return $this->errorResponse([[
                'key' => 'data_not_found',
                'message' => 'Указанная модел не найдена'
            ]], 404, 2);
        }

        if ($exception instanceof AuthenticationException) {
            return $this->errorResponse([
                'key' => 'auth',
                'message' => $exception->getMessage()
            ], 401);
        }

        if ($exception instanceof AuthorizationException) {
            return $this->errorResponse([
                'key' => 'permission',
                'message' => $exception->getMessage()
            ], 403);
        }

        if ($exception instanceof ValidationException) {
            $items = $exception->validator->errors()->getMessages();
            $keys = $exception->validator->failed();
            $errors = [];
            foreach ($items as $field => $message) {
                $messageStandard = [];
                $filedTranslateKeys = array_keys($keys[$field]);
                foreach ($message as $key => $translate) {
                    $messageStandard[] = [
                        'key' => str_replace('app\\rules\\v1\\', '', strtolower(array_shift($filedTranslateKeys))),
                        'text' => $translate
                    ];
                }
                $errors[] = [
                    'field' => $field,
                    'message' => $messageStandard
                ];
            }
            return $this->errorResponse($errors, JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
        return $this->errorResponse(['server_error' => $exception->getMessage()], 500);

    }

    public function errorResponse($errors, $code, $status=null){
        return response()->json([
            'success' => false,
            'status_code' => $status,
            'error' => $errors
        ], $code);
    }

}
