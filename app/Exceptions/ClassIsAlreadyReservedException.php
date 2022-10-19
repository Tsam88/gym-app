<?php

declare(strict_types = 1);

namespace App\Exceptions;

use Illuminate\Http\Response;

final class ClassIsAlreadyReservedException extends AbstractException
{
    protected const DEFAULT_HTTP_CODE = Response::HTTP_CONFLICT;
    protected const DEFAULT_ERROR_MESSAGE = 'Έχετε κάνει ήδη κράτηση στο συγκεκριμένο τμήμα';
    protected const DEFAULT_SEVERITY = 'info';
}
