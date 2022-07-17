<?php

declare(strict_types = 1);

namespace App\Services;

use App\Models\Reservation;
use App\Validators\ReservationValidation;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ReservationService
{
    /**
     * The default number of items per page
     *
     * @var int
     */
    private const DEFAULT_ITEMS_PER_PAGE = 0;

    /**
     * @var ReservationValidation
     */
    private $reservationValidation;

    public function __construct(ReservationValidation $reservationValidation)
    {
        $this->reservationValidation = $reservationValidation;
    }

    /**
     * Return all reservations
     *
     * @param array $input
     *
     * @return LengthAwarePaginator
     */
    public function getReservations(array $input): LengthAwarePaginator
    {
        // data validation
        $data = $this->reservationValidation->reservationGetReservations($input);

        $itemsPerPage = $data['items_per_page'] ?? self::DEFAULT_ITEMS_PER_PAGE;
        $reservations = Reservation::paginate($itemsPerPage);

        return $reservations;
    }

    /**
     * Create a reservation
     *
     * @param array $input Reservation data
     *
     * @return Reservation
     */
    public function create(array $input): Reservation
    {
        // data validation
        $data = $this->reservationValidation->reservationCreate($input);

        // start db transaction
        DB::beginTransaction();

        try {
            $reservation = Reservation::create($data);
        } catch (\Exception $e) {
            // something went wrong, rollback and throw same exception
            DB::rollBack();

            throw $e;
        }

        // commit database changes
        DB::commit();

        return $reservation;
    }

    /**
     * Cancel a reservation
     *
     * @param array $input Reservation data
     *
     * @return void
     */
    public function cancel(array $input, Reservation $reservation)
    {
        // data validation
        $data = $this->reservationValidation->reservationCancel($input);

        // start db transaction
        DB::beginTransaction();

        try {
            $reservation->update($data);
        } catch (\Exception $e) {
            // something went wrong, rollback and throw same exception
            DB::rollBack();

            throw $e;
        }

        // commit database changes
        DB::commit();
    }

    /**
     * Decline a reservation
     *
     * @param array $input Reservation data
     *
     * @return void
     */
    public function decline(array $input, Reservation $reservation)
    {
        // data validation
        $data = $this->reservationValidation->reservationDecline($input);

        // start db transaction
        DB::beginTransaction();

        try {
            $reservation->update($data);
        } catch (\Exception $e) {
            // something went wrong, rollback and throw same exception
            DB::rollBack();

            throw $e;
        }

        // commit database changes
        DB::commit();
    }

    /**
     * Delete reservation
     *
     * @param Reservation $reservation
     *
     * @return void
     */
    public function delete(Reservation $reservation)
    {
        // start db transaction
        DB::beginTransaction();

        try {
            $reservation->delete();
        } catch (\Exception $e) {
            // something went wrong, rollback and throw same exception
            DB::rollBack();

            throw $e;
        }

        // commit database changes
        DB::commit();
    }
}
