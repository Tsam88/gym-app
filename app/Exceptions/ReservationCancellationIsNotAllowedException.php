<?php

declare(strict_types = 1);

namespace App\Exceptions;

use App\Models\Reservation;
use Illuminate\Http\Response;

final class ReservationCancellationIsNotAllowedException extends AbstractException
{
    protected const DEFAULT_HTTP_CODE = Response::HTTP_PRECONDITION_FAILED;
//    protected const DEFAULT_ERROR_MESSAGE = 'Δεν είναι δυνατή η ακύρωση της κράτησης. Η ακύρωση επιτρέπεται μέχρι ' . Reservation::ALLOWED_HOURS_TO_CANCEL_BEFORE_RESERVATION_DATE . ' ώρες πριν την έναρξη του μαθήματος.';
    protected const DEFAULT_ERROR_MESSAGE = 'Booking cancellation is not allowed. Cancellation is allowed up to ' . Reservation::ALLOWED_HOURS_TO_CANCEL_BEFORE_RESERVATION_DATE . ' hours before the start of the course.';
    protected const DEFAULT_SEVERITY = 'info';
}
