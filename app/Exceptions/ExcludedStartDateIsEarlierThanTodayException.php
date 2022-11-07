<?php

declare(strict_types = 1);

namespace App\Exceptions;

use Illuminate\Http\Response;

final class ExcludedStartDateIsEarlierThanTodayException extends AbstractException
{
    protected const DEFAULT_HTTP_CODE = Response::HTTP_CONFLICT;
    protected const DEFAULT_ERROR_MESSAGE = 'Excluded start date is earlier than today';
    protected const DEFAULT_SEVERITY = 'info';
}
