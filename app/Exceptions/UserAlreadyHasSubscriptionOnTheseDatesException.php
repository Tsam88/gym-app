<?php

declare(strict_types = 1);

namespace App\Exceptions;

use Illuminate\Http\Response;

final class UserAlreadyHasSubscriptionOnTheseDatesException extends AbstractException
{
    protected const DEFAULT_HTTP_CODE = Response::HTTP_CONFLICT;
//    protected const DEFAULT_ERROR_MESSAGE = 'Υπάρχει ήδη συνδρομή για την επιλεγμένη ημερομηνία';
    protected const DEFAULT_ERROR_MESSAGE = 'There is already a subscription for the selected date';
    protected const DEFAULT_SEVERITY = 'info';
}
