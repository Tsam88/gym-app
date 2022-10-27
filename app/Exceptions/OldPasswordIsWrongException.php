<?php

declare(strict_types = 1);

namespace App\Exceptions;

use Illuminate\Http\Response;

final class OldPasswordIsWrongException extends AbstractException
{
    protected const DEFAULT_HTTP_CODE = Response::HTTP_EXPECTATION_FAILED;
    protected const DEFAULT_ERROR_MESSAGE = 'Old password is wrong';
    protected const DEFAULT_SEVERITY = 'info';
}
