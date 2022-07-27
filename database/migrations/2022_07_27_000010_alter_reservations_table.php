<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterReservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('declined');
            $table->dropColumn('canceled');
            $table->boolean('declined')->default(false);
            $table->boolean('canceled')->default(false);

            $table->unique(['user_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('declined');
            $table->dropColumn('canceled');
            $table->integer('declined');
            $table->integer('canceled');

            $table->dropUnique(['user_id', 'date']);
        });
    }
}
