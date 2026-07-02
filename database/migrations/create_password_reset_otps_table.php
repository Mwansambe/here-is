<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('password_reset_otps', function (Blueprint $table) {
            $table->id();

            // One of these will be used:
            $table->string('email')->nullable()->index();
            $table->string('phone', 20)->nullable()->index();

            $table->string('code_hash'); // hashed OTP
            $table->timestamp('expires_at');
            $table->timestamp('verified_at')->nullable();
            $table->unsignedSmallInteger('attempts')->default(0);

            $table->timestamps();

            $table->index(['email', 'phone', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_reset_otps');
    }
};