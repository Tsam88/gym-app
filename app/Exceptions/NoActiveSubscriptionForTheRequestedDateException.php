<?php

declare(strict_types = 1);

namespace App\Exceptions;

use Illuminate\Http\Response;

final class NoActiveSubscriptionForTheRequestedDateException extends AbstractException
{
    protected const DEFAULT_HTTP_CODE = Response::HTTP_PRECONDITION_FAILED;
    protected const DEFAULT_ERROR_MESSAGE = 'Δεν υπάρχει ενεργή συνδρομή για την ημερομηνία που ζητήσατε';
    protected const DEFAULT_SEVERITY = 'info';
}
