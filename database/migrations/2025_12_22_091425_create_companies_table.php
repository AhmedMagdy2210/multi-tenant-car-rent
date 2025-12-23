<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('domain')->unique();
            $table->string('database_name')->unique();
            $table->string('database_host')->nullable();
            $table->string('database_port')->nullable();
            $table->string('database_username');
            $table->string('database_password');
            $table->json('settings')->nullable();
            $table->string('country');
            $table->string('city');
            $table->string('address');
            $table->string('timezone');
            $table->string('currency');
            $table->enum('status', ['pending', 'active', 'suspended', 'trial', 'canclled', 'expired'])->default('pending');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
