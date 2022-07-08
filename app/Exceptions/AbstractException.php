<?php

declare(strict_types = 1);

namespace App\Exceptions;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Lang;

abstract class AbstractException extends \Exception
{
    public const TRANSLATION_PREFIX = 'exception';

    protected const DEFAULT_HTTP_CODE = Response::HTTP_INTERNAL_SERVER_ERROR;
    protected const DEFAULT_ERROR_MESSAGE = 'server-error';
    protected const DEFAULT_SEVERITY = 'error';

    /**
     * Exception http code
     *
     * @var int
     */
    protected $httpCode;

    /**
     * Exception message
     *
     * @var string
     */
    protected $message;

     /**
     * Exception severity
     *
     * @var string
     */
    protected $severity;

    /**
     * Constructor.
     *
     * @param string|null  $message
     * @param integer|null $code
     * @param array|null   $errors
     * @param string|null  $severity
     * @param \Throwable   $previous
     */
    public function __construct(string $message = null, int $code = null, array $errors = null, string $severity = null, \Throwable $previous = null)
    {
        $this->httpCode = $code ?? static::DEFAULT_HTTP_CODE;
        $this->message = $message ?? static::DEFAULT_ERROR_MESSAGE;
        $this->errors = $this->buildErrors($errors);
        $this->severity = $severity ?? static::DEFAULT_SEVERITY;

        parent::__construct($this->message, $this->httpCode, $previous);
    }

    /**
     * Get Exception Http Code
     *
     * @return int
    */
    public function getHttpCode(): int
    {
        return $this->code;
    }

    /**
     * Get exception message
     *
     * @param bool $translate Defines if message will be translated.
     *
     * @return string
     */
    public function getExceptionMessage(bool $translate = false): string
    {
        if ($translate) {
            return Lang::get(sprintf('%s.message.%s', static::TRANSLATION_PREFIX, $this->message));
        } else {
            return $this->message;
        }
    }

    /**
     * Get exception errors
     *
     * @return array|null
     */
    public function getExceptionErrors(bool $translate = false): ?array
    {
        $errors = $this->errors ?? [];

        foreach ($errors as $attribute => &$messages) {
            foreach ($messages as &$message) {
                // translate messages if required.
                if (true === $translate) {
                    $message = Lang::get(sprintf('%s.message.%s', static::TRANSLATION_PREFIX, $message));
                }

                // replace placeholders
                $message = \str_replace(':attribute', $attribute, $message);
            }
        }

        $this->errors = $errors;

        return $this->errors;
    }

    /**
     * Get Exception Severity.
     *
     * @return string
    */
    public function getSeverity(): string
    {
        return $this->severity;
    }

    /**
     * Get Exception Default Error Message.
     *
     * @param bool $translate Defines if message will be translated.
     *
     * @return string
    */
    public function getDefaultErrorMessage(bool $translate = false): string
    {
        if ($translate) {
            return Lang::get(sprintf('%s.message.%s', static::TRANSLATION_PREFIX, static::DEFAULT_ERROR_MESSAGE));
        } else {
            return static::DEFAULT_ERROR_MESSAGE;
        }
    }

    /**
     * Define if Error message should be translated before sending response to client.
     *
     * @return bool
     */
    public function shouldTranslateErrorMessage(): bool
    {
        return true;
    }

    /**
     * Build exception errors in order to match Laravel validation error structure.
     *
     * @param array|null $errors
     *
     * @return array|null
     */
    private function buildErrors(?array $errors): ?array
    {
        if (null === $errors) {
            return null;
        }

        return [$errors['property'] => $errors['messages']];
    }
}
