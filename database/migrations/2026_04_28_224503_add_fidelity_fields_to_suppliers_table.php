<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('contact_person')->nullable()->after('name');
            $table->enum('category', ['Alimento', 'Accesorios', 'Higiene', 'Otros'])->default('Otros')->after('contact_person');
            $table->enum('status', ['Active', 'Inactive'])->default('Active')->after('address');
        });
    }

    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn(['contact_person', 'category', 'status']);
        });
    }
};
