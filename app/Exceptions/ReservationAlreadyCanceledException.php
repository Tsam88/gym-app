<?php

declare(strict_types = 1);

namespace App\Exceptions;

use App\Models\Reservation;
use Illuminate\Http\Response;

final class ReservationAlreadyCanceledException extends AbstractException
{
    protected const DEFAULT_HTTP_CODE = Response::HTTP_CONFLICT;
    protected const DEFAULT_ERROR_MESSAGE = 'Η κράτηση έχει ήδη ακυρωθεί.';
    protected const DEFAULT_SEVERITY = 'info';
}
