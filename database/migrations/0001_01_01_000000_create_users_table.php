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
        // Schema::create('users', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('username')->unique()->nullable()->comment('Unique username for the user');
        //     $table->string('userid')->unique()->nullable()->comment('Unique userid for the user');
        //     $table->string('passwordHash')->nullable()->comment('Hashed password');
        //     $table->string('email')->unique()->nullable()->comment('User email address');
        //     $table->enum('role', ['admin', 'screener', 'supervisor'])->nullable()->comment('User role for access control');
            

        //     // Foreign Key referencing points_of_entry table
        //     $table->unsignedBigInteger('poeId')->nullable()->comment('Associated Point of Entry ID');
        //     $table->foreign('poeId')->references('id')->on('points_of_entry')->onDelete('set null');


        //     $table->dateTime('lastLogin')->nullable()->comment('Timestamp of last login');
            
        //     // Auto timestamps columns
        //     $table->timestamp('createdAt')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('Record creation timestamp');
        //     $table->timestamp('updatedAt')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('Record last update timestamp');

        //     // Laravel's optional features for the users table
        //     $table->rememberToken();
        // });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
