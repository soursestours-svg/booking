<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table("bookings", function (Blueprint $table) {
            $table->string("payment_status")->default("pending")->after("status");
            $table->string("payment_id")->nullable()->after("payment_status");
            $table->string("payment_method")->nullable()->after("payment_id");
        });
    }

    public function down(): void
    {
        Schema::table("bookings", function (Blueprint $table) {
            $table->dropColumn(["payment_status", "payment_id", "payment_method"]);
        });
    }
};