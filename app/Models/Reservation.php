<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'gym_class_id',
        'week_day_id',
        'declined',
        'canceled',
    ];

    /**
     * The attributes that are hidden.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    /**
     * Many to One association for SubscriptionPlan
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subscriptionPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    /**
     * Many to One association for User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Many to One association for GymClass
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function gymClass()
    {
        return $this->belongsTo(GymClass::class);
    }

    /**
     * Many to One association for WeekDay
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function weekDay()
    {
        return $this->belongsTo(WeekDay::class);
    }
}
