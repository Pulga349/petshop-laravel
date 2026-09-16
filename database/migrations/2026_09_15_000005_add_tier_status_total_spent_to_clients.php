<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('tier')->default('Bronze')->after('address');
            $table->string('status')->default('active')->after('tier');
            $table->decimal('total_spent', 10, 2)->default(0)->after('status');
            $table->index('tier');
            $table->index('status');
        });

        // Data migration: calculate total_spent from SUM(sales.total) and set tier
        $clients = DB::table('clients')->get();
        foreach ($clients as $client) {
            $totalSpent = DB::table('sales')
                ->where('client_id', $client->id)
                ->sum('total');

            $tier = match(true) {
                $totalSpent >= 10000 => 'Platinum',
                $totalSpent >= 5000 => 'Gold',
                $totalSpent >= 1000 => 'Silver',
                default => 'Bronze',
            };

            DB::table('clients')
                ->where('id', $client->id)
                ->update([
                    'total_spent' => $totalSpent,
                    'tier' => $tier,
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropIndex('clients_tier_index');
            $table->dropIndex('clients_status_index');
            $table->dropColumn(['tier', 'status', 'total_spent']);
        });
    }
};
