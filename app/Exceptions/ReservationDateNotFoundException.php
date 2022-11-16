<?php

declare(strict_types = 1);

namespace App\Exceptions;

use Illuminate\Http\Response;

final class ReservationDateNotFoundException extends AbstractException
{
    protected const DEFAULT_HTTP_CODE = Response::HTTP_NOT_FOUND;
//    protected const DEFAULT_ERROR_MESSAGE = 'Δεν υπάρχει τμήμα για την ημερομηνία που επιλέξατε';
    protected const DEFAULT_ERROR_MESSAGE = 'There is no class for the selected date';
    protected const DEFAULT_SEVERITY = 'info';
}
