<?php

declare(strict_types = 1);

namespace App\Exceptions;

use Illuminate\Http\Response;

final class NotFoundException extends AbstractException
{
    protected const DEFAULT_HTTP_CODE = Response::HTTP_NOT_FOUND;
    protected const DEFAULT_ERROR_MESSAGE = 'Not found';
    protected const DEFAULT_SEVERITY = 'warning';
}
