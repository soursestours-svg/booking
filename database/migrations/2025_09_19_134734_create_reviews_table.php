<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("reviews", function (Blueprint $table) {
            $table->id();
            $table->foreignId("service_id")->constrained()->onDelete("cascade");
            $table->foreignId("user_id")->constrained()->onDelete("cascade");
            $table->foreignId("booking_id")->nullable()->constrained()->onDelete("set null");
            $table->tinyInteger("rating")->unsigned()->default(5);
            $table->text("comment")->nullable();
            $table->boolean("is_approved")->default(false);
            $table->boolean("is_visible")->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(["service_id", "is_approved", "is_visible"]);
            $table->index(["user_id", "service_id"]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("reviews");
    }
};