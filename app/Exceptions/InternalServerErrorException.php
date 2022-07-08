<?php

declare(strict_types = 1);

namespace App\Exceptions;

use Illuminate\Http\Response;

final class InternalServerErrorException extends AbstractException
{
    protected const DEFAULT_HTTP_CODE = Response::HTTP_INTERNAL_SERVER_ERROR;
    protected const DEFAULT_ERROR_MESSAGE = 'server-error';
    protected const DEFAULT_SEVERITY = 'critical';
}
