<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('customer_id', 50)->nullable()->default("simulation_customer_id");;
            $table->string('payment_method_id', 50)->nullable()->default("simulation_payment_id");;
            $table->unsignedBigInteger('current_plan_id')->nullable()->after('payment_method_id');
            $table->integer('xp')->default(0);
            $table->foreign('current_plan_id')->references('id')->on('plans')->onDelete('set null');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
