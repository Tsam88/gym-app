<?php

declare(strict_types=1);

namespace App\Libraries;

use App\Models\Reservation;
use App\Models\Subscription;
use Carbon\Carbon;

class ReservationSubscriptionHelper
{
    /**
     * Check if there is active subscription
     *
     * @param int      $userId
     * @param string   $startsAt
     * @param string   $expiresAt
     * @param int|null $excludedSubscriptionId
     * @param bool     $lastReservationDate
     *
     * @return bool
     */
    public function hasSubscriptionDateConflict(int $userId, string $startsAt, string $expiresAt, ?int $excludedSubscriptionId): bool
    {
        $lastReservationDate = $this->getLastReservationDate($userId);

        $today = Carbon::today('Europe/Athens');

        if (!empty($lastReservationDate) && $lastReservationDate >= $today) {
            // get subscriptions that the expiration date has not passed,
            // but the remaining sessions are zero and the last sessions belongs in the past (so the user can not cancel it)
            $subscriptionStillActiveWithNoRemaining = Subscription::where('user_id', $userId)
                ->where('unlimited_sessions', false)
                ->where('remaining_sessions', '<=', 0)
                ->where('starts_at', '<=', $today)
                ->where('expires_at', '>=', $today)
                ->where('starts_at', '<=', $lastReservationDate)
                ->where('expires_at', '>=', $lastReservationDate)
                ->first();
        } else {
            $subscriptionStillActiveWithNoRemaining = null;
        }

        // subscription with no unlimited setting on and no remaining sessions, supposed expired sessions
        $subscriptionIdsWithNoRemainingSessions = Subscription::where('unlimited_sessions', false)
            ->where('remaining_sessions', '<=', 0)
            ->when($subscriptionStillActiveWithNoRemaining, function ($query) use ($subscriptionStillActiveWithNoRemaining) {
                $query->where('id', '!=', $subscriptionStillActiveWithNoRemaining->id);
            })
            ->pluck('id');

        $hasSubscriptionDateConflict = Subscription::where('user_id', $userId)
            ->where(function ($query) use ($startsAt, $expiresAt) {
                $query->whereBetween('starts_at', [$startsAt, $expiresAt])
                    ->orWhereBetween('expires_at', [$startsAt, $expiresAt])
                    ->orWhere(function ($query) use ($startsAt, $expiresAt) {
                        $query->where('starts_at', '<=', $startsAt)
                            ->Where('expires_at', '>=', $expiresAt);
                    });
            })
            ->where('id', '!=', $excludedSubscriptionId)
            ->whereNotIn('id', $subscriptionIdsWithNoRemainingSessions)
            ->exists();

        return $hasSubscriptionDateConflict;
    }

    /**
     * Get user's active subscription
     *
     * @param int $userId
     *
     * @return Subscription|null
     */
    public function getClosestActiveSubscription(int $userId): ?Subscription
    {
        $lastReservationDate = $this->getLastReservationDate($userId);

        $today = Carbon::today('Europe/Athens');

        if (!empty($lastReservationDate) && $lastReservationDate >= $today) {
            // get subscriptions that the expiration date has not passed,
            // but the remaining sessions are zero and the last sessions belongs in the past (so the user can not cancel it)
            $subscriptionStillActiveWithNoRemaining = Subscription::where('user_id', $userId)
                ->where('unlimited_sessions', false)
                ->where('remaining_sessions', '<=', 0)
                ->where('starts_at', '<=', $today)
                ->where('expires_at', '>=', $today)
                ->where('starts_at', '<=', $lastReservationDate)
                ->where('expires_at', '>=', $lastReservationDate)
                ->first();

            if ($subscriptionStillActiveWithNoRemaining) {
                return $subscriptionStillActiveWithNoRemaining;
            }
        }

        $closetActiveSubscriptionForUser = Subscription::where('user_id', $userId)
            ->where(function ($query) {
                $query->where('unlimited_sessions', true)
                    ->orWhere(function ($query) {
                        $query->where('unlimited_sessions', false)
                            ->where('remaining_sessions', '>', 0);
                    });
            })
            ->where('expires_at', '>=', $today)
            ->orderBy('starts_at', 'ASC')
            ->first();

        return $closetActiveSubscriptionForUser;
    }

    /**
     * Get user's active subscription for the requested reservation date, if exists
     *
     * @param int    $userId
     * @param string $reservationDate
     *
     * @return Subscription|null
     */
    public function getActiveSubscriptionForReservationDate(int $userId, string $reservationDate): ?Subscription
    {
        $reservationDateCarbon = Carbon::parse($reservationDate, 'Europe/Athens');

        $today = Carbon::today('Europe/Athens');

        // if requested reservation date is before today, then return null
        if ($reservationDateCarbon < $today) {
            return null;
        }

        $lastReservationDate = $this->getLastReservationDate($userId);

        if (!empty($lastReservationDate) && $lastReservationDate >= $today) {
            // get subscriptions that the expiration date has not passed,
            // but the remaining sessions are zero and the last sessions belongs in the past (so the user can not cancel it)
            $subscriptionStillActiveWithNoRemaining = Subscription::where('user_id',$userId)
                ->where('unlimited_sessions', false)
                ->where('remaining_sessions', '<=', 0)
                ->where('starts_at', '<=', $today)
                ->where('expires_at', '>=', $today)
                ->where('starts_at', '<=', $reservationDate)
                ->where('expires_at', '>=', $reservationDate)
                ->where('starts_at', '<=', $lastReservationDate)
                ->where('expires_at', '>=', $lastReservationDate)
                ->first();

            if ($subscriptionStillActiveWithNoRemaining) {
                return $subscriptionStillActiveWithNoRemaining;
            }
        }

        // get subscription where the requested reservation date is between subscription's start and end dates
        // and subscription has unlimited sessions or doesn't have unlimited session, but there are remaining sessions
        $activeSubscriptionForReservationDate = Subscription::where('user_id', $userId)
            ->where(function ($query) {
                $query->where('unlimited_sessions', true)
                    ->orWhere(function ($query) {
                        $query->where('unlimited_sessions', false)
                            ->where('remaining_sessions', '>', 0);
                    });
            })
            ->where('starts_at', '<=', $reservationDate)
            ->where('expires_at', '>=', $reservationDate)
            ->orderBy('starts_at', 'ASC')
            ->first();

        return $activeSubscriptionForReservationDate;
    }

    /**
     * Get user's last reservation date
     *
     * @param int $userId
     *
     * @return Carbon|null
     */
    public function getLastReservationDate(int $userId): ?Carbon
    {
        $lastReservation = Reservation::where('user_id', $userId)
            ->where('canceled', false)
            ->where('declined', false)
            ->orderBy('date', 'DESC')
            ->first();

        $lastReservationDate = $lastReservation ? Carbon::parse($lastReservation->date, 'Europe/Athens') : null;

        return $lastReservationDate;
    }
}
