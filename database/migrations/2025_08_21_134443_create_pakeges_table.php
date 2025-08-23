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
        Schema::create('pakeges', function (Blueprint $table) {
            $table->id();
            $table->string("image");
            $table->json("title")->nullable();
            $table->json("description")->nullable();
            $table->decimal("amount_from" , 10,2)->default(0);
            $table->decimal("amount_to" , 10,2)->default(0);
            $table->decimal("support_percentage" , 10,2)->default(0);
            $table->decimal("price" , 10,2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pakeges');
    }
};
