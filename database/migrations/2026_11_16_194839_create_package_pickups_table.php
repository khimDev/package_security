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
        Schema::create('package_pickups', function (Blueprint $table) {
            $table->id();

            $table->foreignId('package_id')
                ->unique()
                ->constrained('packages')
                ->restrictOnDelete();

            $table->string('taker_name');

            $table->foreignId('served_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->string('signature_path');

            $table->timestamp('picked_up_at');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_pickups');
    }
};
