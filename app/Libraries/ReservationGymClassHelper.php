<?php

declare(strict_types=1);

namespace App\Libraries;

use App\Models\GymClass;
use App\Models\Reservation;

class ReservationGymClassHelper
{
    /**
     * Check if class is full for specific date
     *
     * @param int    $gymClassId
     * @param int    $weekDayId
     * @param string $date
     *
     * @return bool
     */
    public function isGymClassFull(int $gymClassId, int $weekDayId, string $date): bool
    {
        $countGymClassReservations = $this->countGymClassReservations($gymClassId, $weekDayId, $date);

        $gymClassStudentLimit = GymClass::where('id', $gymClassId)->first()->number_of_students;

        $isGymClassFull = $countGymClassReservations >= $gymClassStudentLimit;

        return $isGymClassFull;
    }

    /**
     * Count gym class reservations for specific date
     *
     * @param int    $gymClassId
     * @param int    $weekDayId
     * @param string $date
     *
     * @return int
     */
    public function countGymClassReservations(int $gymClassId, int $weekDayId, string $date): int
    {
        $countGymClassReservations = Reservation::where('gym_class_id', $gymClassId)
            ->where('week_day_id', $weekDayId)
            ->where('date', $date)
            ->where('canceled', false)
            ->where('declined', false)
            ->count();

        return $countGymClassReservations;
    }
}
