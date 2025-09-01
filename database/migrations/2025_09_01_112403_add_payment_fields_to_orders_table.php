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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_id')->nullable()->after('coingate_id');
            $table->text('address_in')->nullable()->after('payment_id');
            $table->text('ipn_token')->nullable()->after('address_in');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
                        $table->dropColumn(['payment_id', 'address_in' , 'ipn_token']);

        });
    }
};
