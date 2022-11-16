<?php

declare(strict_types = 1);

namespace App\Exceptions;

use Illuminate\Http\Response;

final class SessionsWeekLimitExceededException extends AbstractException
{
    protected const DEFAULT_HTTP_CODE = Response::HTTP_PRECONDITION_FAILED;
//    protected const DEFAULT_ERROR_MESSAGE = 'Έχετε ξεπεράσει το εβδομαδιαίο όριο συνεδριών';
    protected const DEFAULT_ERROR_MESSAGE = 'You have exceeded your weekly session limit';
    protected const DEFAULT_SEVERITY = 'info';
}
