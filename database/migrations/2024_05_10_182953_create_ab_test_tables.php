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
        // Create ab_tests table
        Schema::create('ab_tests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('status', ['ready', 'started', 'stopped']);
            $table->timestamps();
        });

        // Create ab_test_variants table
        Schema::create('ab_test_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ab_test_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->float('targeting_ratio');
            $table->timestamps();
        });

        // Create sessions_ab_test_variants table
        Schema::create('sessions_ab_test_variants', function (Blueprint $table) {
            $table->foreignId('session_id')->constrained()->onDelete('cascade');
            $table->foreignId('ab_test_variant_id')->constrained()->onDelete('cascade');
            $table->primary(['session_id', 'ab_test_variant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions_ab_test_variants');
        Schema::dropIfExists('ab_test_variants');
        Schema::dropIfExists('ab_tests');
    }
};
