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
        Schema::table('sales', function (Blueprint $table) {
            // Nullable so existing rows stay valid; new store validation requires payment_method.
            $table->string('payment_method')->nullable()->after('total');
            $table->decimal('amount_paid', 10, 2)->nullable()->after('payment_method');
            // total = grand total after discount (VAT included in prices).
            $table->decimal('discount_amount', 10, 2)->default(0)->after('amount_paid');
            $table->decimal('iva_amount', 10, 2)->default(0)->after('discount_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'amount_paid', 'discount_amount', 'iva_amount']);
        });
    }
};
