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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("phone");
            $table->string("bank_name");
            $table->string("bank_iban");
            $table->string("support_for");
            $table->decimal("total_need_amount" , 10,2)->default(0);
            $table->decimal("support_percentage" , 10,2)->default(0);
            $table->decimal("support_amount" , 10,2)->default(0);
            $table->decimal("amount" , 10,2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
