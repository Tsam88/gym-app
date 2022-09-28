<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param Throwable  $exception
     *
     * @return void
     */
    public function report(Throwable $exception)
    {
        // Exception handler logs all exceptions as errors.
        // we don't want that
        // we will log all exception in render method
        // do nothing here
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Throwable  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {
        // sometimes we may need to get the exception message and always return a generic message to use
        // for example for internal server errors.
        // keep exception message there and log this if exist
        $devMessage = null;

        $headers = [];

        try {
            // get logger from container
            $logger = $this->container->make(LoggerInterface::class);
        } catch (Exception $ex) {
            throw $exception;
        }

        $error = [
            'code' => 500,
            'severity' => 'error',
        ];

        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
            $error['code'] = 404;
            $error['message'] = "Not found";
            $error['severity'] = 'warning';
        } elseif ($exception instanceof ModelNotFoundException) {
            $error['code'] = 404;
            $error['message'] = "Not found";
            $error['severity'] = 'warning';
        } elseif ($exception instanceof  \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException) {
            // in case of basic authentication failure (used by telescope), pass the headers
            $headers = $exception->getHeaders();

            $error['code'] = 401;
            $error['message'] = "Unauthorized";
            $error['severity'] = 'warning';
        } elseif ($exception instanceof \Illuminate\Auth\AuthenticationException) {
            $error['code'] = 401;
            $error['message'] = "Unauthorized";
            $error['severity'] = 'warning';
        } elseif ($exception instanceof \Illuminate\Routing\Exceptions\InvalidSignatureException) {
            $error['code'] = 401;
            $error['message'] = "Unauthorized";
            $error['severity'] = 'warning';
        } elseif ($exception instanceof \Illuminate\Auth\Access\AuthorizationException) {
            $error['code'] = 403;
            $error['message'] = "Forbidden";
            $error['severity'] = 'critical';
        } elseif ($exception instanceof \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException) {
            $error['code'] = 403;
            $error['message'] = "Forbidden";
            $error['severity'] = 'critical';
        } elseif ($exception instanceof \Illuminate\Validation\ValidationException) {
            $error['code'] = 422;
            $error['message'] = 'Validation error';
            $error['errors'] = $exception->errors();
            $error['severity'] = 'info';
        } elseif ($exception instanceof MethodNotAllowedHttpException) {
            $error['code'] = 405;
            $error['message'] = 'Method not allowed';
            $error['severity'] = 'warning';
        } elseif ($exception instanceof \Illuminate\Http\Exceptions\ThrottleRequestsException) {
            $error['code'] = 429;
            $error['message'] = 'Too many requests';
            $error['severity'] = 'warning';
        } elseif ($exception instanceof \App\Exceptions\InternalServerErrorException) {
            $error['code'] = $exception->getHttpCode();
            $devMessage = $exception->getExceptionMessage();
            $error['message'] = $exception->getDefaultErrorMessage(true);
            $error['severity'] = $exception->getSeverity();
        } elseif ($exception instanceof \App\Exceptions\AbstractException) {
            $error['code'] = $exception->getHttpCode();
            $error['message'] = $exception->getExceptionMessage();
            $error['severity'] = $exception->getSeverity();

            // check if exception has it's own errors.
            $exceptionErrors = $exception->getExceptionErrors(true);
            if (null !== $exceptionErrors) {
                $error['errors'] = $exceptionErrors;
            }
        } else {
            $error['code'] = 500;
            $error['message'] = "Unexpected error";
            $error['severity'] = 'alert';
            $error['errors'] = $exception->getMessage();
        }

        // log error
        $logger->log(
            $error['severity'],
            ($devMessage ?? $error['message']),
            array_merge($this->context(), ['exception' => $exception], ['errors' => ($error['errors'] ?? [])]),
            );

        // no need to pass severity to client
        unset($error['severity']);

        return new JsonResponse($error, $error['code'], $headers);
    }
}
