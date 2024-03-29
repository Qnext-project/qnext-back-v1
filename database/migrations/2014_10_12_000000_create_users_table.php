<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->foreignId('clinic_id')->constrained('clinics');
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->enum('role', ['admin', 'doctor'])->default('doctor');
            $table->text('acl')->nullable();
            $table->double('current_turn_number')->nullable();
            $table->time('current_turn_time')->nullable();
            $table->boolean('is_super_admin')->default(0);
            $table->text('socket_id')->nullable();
            $table->foreignId('room_id')->nullable()->constrained('rooms');
            $table->foreignId('doctor_id')->nullable()->constrained('users');
            $table->foreignId('media_id')->nullable()->constrained('medias');
            $table->foreignId('title_id')->nullable()->constrained('expertises');
            $table->foreignId('expertise_id')->nullable()->constrained('expertises');
            $table->string('fpass')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['username', 'clinic_id']);
        });
        User::create([
            'first_name' => 'مهرداد',
            'last_name' => 'مقدسی',
            'username' => 'moghadasi',
            'password' => Hash::make('moghadasi@123'),
            'role' => 'admin',
            'is_super_admin' => 1,
            'clinic_id' => 1,
            'fpass' => 'moghadasi@123',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
