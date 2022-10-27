<?php

declare(strict_types = 1);

namespace App\Exceptions;

use Illuminate\Http\Response;

final class PasswordIsWrongException extends AbstractException
{
    protected const DEFAULT_HTTP_CODE = Response::HTTP_EXPECTATION_FAILED;
    protected const DEFAULT_ERROR_MESSAGE = 'Password is wrong';
    protected const DEFAULT_SEVERITY = 'info';
}
