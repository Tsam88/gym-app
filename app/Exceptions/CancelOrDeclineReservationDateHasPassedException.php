<?php

declare(strict_types = 1);

namespace App\Exceptions;

use Illuminate\Http\Response;

final class CancelOrDeclineReservationDateHasPassedException extends AbstractException
{
    protected const DEFAULT_HTTP_CODE = Response::HTTP_PRECONDITION_FAILED;
//    protected const DEFAULT_ERROR_MESSAGE = 'Η ενέργεια δεν μπορεί να ολοκληρωθεί. Η ημερομηνία κράτησης έχει παρέλθει.';
    protected const DEFAULT_ERROR_MESSAGE = 'This action can not be completed. The booking date belongs in the past.';
    protected const DEFAULT_SEVERITY = 'info';
}
