<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GymClass extends Model
{
    use SoftDeletes;

    /**
     * The number of models to return for pagination.
     *
     * @var int
     */
    protected $perPage = 10000;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'teacher',
        'number_of_students',
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
     * One to Many association for WeekDay
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function weekDays()
    {
        return $this->hasMany(WeekDay::class);
    }

    /**
     * One to Many association for Reservation
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
