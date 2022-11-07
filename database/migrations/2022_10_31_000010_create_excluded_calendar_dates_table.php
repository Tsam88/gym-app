<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExcludedCalendarDatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('excluded_calendar_dates', function (Blueprint $table) {
            $table->id();
            $table->dateTime('start_date')->index();
            $table->dateTime('end_date')->index();
            $table->boolean('extend_subscription')->default(false);;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('excluded_calendar_dates');
    }
}
