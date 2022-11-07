<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExcludedCalendarDate extends Model
{
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'start_date' => 'date:Y-m-d',
        'end_date' => 'date:Y-m-d',
        'extend_subscription' => 'boolean',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'start_date',
        'end_date',
        'extend_subscription',
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
     * Many to Many association for GymClass
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function gymClasses()
    {
        return $this->belongsToMany(GymClass::class)->withTimestamps();
    }
}
