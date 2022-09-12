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
    public function hasActiveSubscription(int $userId, string $startsAt, string $expiresAt, ?int $excludedSubscriptionId): bool
    {
        $lastReservationDate = $this->getLastReservationDate($userId);

        $today = Carbon::today('Europe/Athens');

        if (!empty($lastReservationDate) && $today >= $lastReservationDate) {
            // get subscriptions that the expiration date has not passed,
            // but the remaining sessions are zero and the last sessions belongs in the past (so the user can not cancel it)
            $subscriptionIdsWithNoRemainingSessions = Subscription::where('unlimited_sessions', false)
                ->where('remaining_sessions', '<=', 0)
                ->where('starts_at', '<=', $today)
                ->where('expires_at', '>=', $today)
                ->where('starts_at', '<=', $lastReservationDate)
                ->where('expires_at', '>=', $lastReservationDate)
                ->pluck('id');
        } else {
            $subscriptionIdsWithNoRemainingSessions = [];
        }

        $activeSubscription = Subscription::where('user_id',$userId)
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

        return $activeSubscription;
    }

    /**
     * Get user's active subscription
     *
     * @param int $userId
     *
     * @return Subscription|null
     */
    public function getActiveSubscription(int $userId): ?Subscription
    {
        $lastReservationDate = $this->getLastReservationDate($userId);

        $today = Carbon::today('Europe/Athens');

        if (!empty($lastReservationDate) && $today >= $lastReservationDate) {
            // get subscriptions that the expiration date has not passed,
            // but the remaining sessions are zero and the last sessions belongs in the past (so the user can not cancel it)
            $subscriptionIdsWithNoRemainingSessions = Subscription::where('unlimited_sessions', false)
                ->where('remaining_sessions', '<=', 0)
                ->where('starts_at', '<=', $today)
                ->where('expires_at', '>=', $today)
                ->where('starts_at', '<=', $lastReservationDate)
                ->where('expires_at', '>=', $lastReservationDate)
                ->pluck('id');
        } else {
            $subscriptionIdsWithNoRemainingSessions = [];
        }

        $activeSubscription = Subscription::where('user_id',$userId)
            ->where('starts_at', '<=', $today)
            ->where('expires_at', '>=', $today)
            ->whereNotIn('id', $subscriptionIdsWithNoRemainingSessions)
            ->first();

        return $activeSubscription;
    }

    /**
     * Check if the subscription is active
     *
     * @param Subscription $subscription
     *
     * @return bool
     */
    public function isActiveSubscription(Subscription $subscription): bool
    {
        $now = Carbon::now('Europe/Athens');
        $dateLimit = clone $now->subHours(Reservation::ALLOWED_HOURS_TO_CANCEL_BEFORE_RESERVATION_DATE);
        $startsAt = Carbon::parse($subscription->starts_at->format('Y-m-d'), 'Europe/Athens');
        $expiresAt = Carbon::parse($subscription->expires_at->format('Y-m-d'), 'Europe/Athens');
        $lastReservationDate = $this->getLastReservationDate($subscription->user_id);

        if ($now >= $startsAt && $now <= $expiresAt
            && ($subscription->unlimited_sessions || $subscription->remaining_sessions > 0
                || ($subscription->remaining_sessions <= 0 && !empty($lastReservationDate) && $lastReservationDate <= $dateLimit))) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get user's last reservation date
     *
     * @param int $userId
     *
     * @return Carbon|null
     */
    private function getLastReservationDate(int $userId): ?Carbon
    {
        $lastReservation = Reservation::where('user_id', $userId)
            ->where('canceled', false)
            ->where('declined', false)
            ->orderBy('date', 'DESC')
            ->first();

        $lastReservationDate = $lastReservation ? Carbon::parse($lastReservation->date) : null;

        return $lastReservationDate;
    }
}
