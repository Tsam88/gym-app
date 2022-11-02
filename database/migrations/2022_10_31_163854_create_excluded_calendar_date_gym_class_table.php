<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExcludedCalendarDateGymClassTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('excluded_calendar_date_gym_class', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gym_class_id')->index();
            $table->unsignedBigInteger('excluded_calendar_date_id')->index();

            $table->foreign('gym_class_id')
                ->references('id')->on('gym_classes')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('excluded_calendar_date_id', 'excluded_date_gym_class_excluded_date_id_foreign')
                ->references('id')->on('excluded_calendar_dates')
                ->onUpdate('cascade')
                ->onDelete('cascade');

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
        Schema::dropIfExists('excluded_calendar_date_gym_class');
    }
}
