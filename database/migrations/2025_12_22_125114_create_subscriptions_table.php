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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('plan_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('billing_cycle', ['monthly', 'yearly']);
            $table->decimal('price', 10, 2);
            $table->string('currency');
            $table->enum('status', ['active', 'pending', 'canclled', 'expired', 'past_due'])->default('pending');
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->timestamp('canclled_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
