<?php

declare(strict_types = 1);

namespace App\Exceptions;

use Illuminate\Http\Response;

final class ExcludedDatesCoincideWithAnotherExcludedDateException extends AbstractException
{
    protected const DEFAULT_HTTP_CODE = Response::HTTP_CONFLICT;
    protected const DEFAULT_ERROR_MESSAGE = 'The excluded dates coincide with another excluded date, for gym class ';
    protected const DEFAULT_SEVERITY = 'info';

    /**
     * @inheritDoc
     */
    public function __construct(string $message = null, int $code = null, array $errors = null, string $severity = null, \Throwable $previous = null)
    {
        $message = self::DEFAULT_ERROR_MESSAGE . '"' . $message . '"';

        parent::__construct($message, $code, $errors, $severity, $previous);
    }
}
