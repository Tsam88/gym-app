<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubscriptionPlan extends Model
{
    use SoftDeletes;

    /**
     * The number of models to return for pagination.
     *
     * @var int
     */
    protected $perPage = 10000;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'plan_price' => 'float',
        'unlimited_sessions' => 'boolean',
        'display_on_page' => 'boolean',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'plan_price',
        'number_of_sessions',
        'number_of_months',
        'unlimited_sessions',
        'display_on_page',
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
     * One to Many association for Subscription
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscriptions::class);
    }
}
