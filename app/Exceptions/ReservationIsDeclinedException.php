<?php

declare(strict_types = 1);

namespace App\Exceptions;

use Illuminate\Http\Response;

final class ReservationIsDeclinedException extends AbstractException
{
    protected const DEFAULT_HTTP_CODE = Response::HTTP_PRECONDITION_FAILED;
    protected const DEFAULT_ERROR_MESSAGE = 'Η συγκεκριμένη κράτηση έχει απορριφθεί';
    protected const DEFAULT_SEVERITY = 'info';
}
