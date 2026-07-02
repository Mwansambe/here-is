<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone',20)->nullable()->unique()->after('email');
            $table->string('role',20)->default('customer')->after('password');
            $table->timestamp('phone_verified_at')->nullable()->after('email_verified_at');
        });
    }
    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone','role','phone_verified_at']);
        });
    }
};