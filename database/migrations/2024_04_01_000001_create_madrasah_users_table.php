<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('madrasah_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('madrasah_id')
                ->constrained('madrasah')
                ->onDelete('cascade');
            $table->string('nsm')->unique()->comment('NSM digunakan sebagai username login');
            $table->string('nama_pic')->comment('Nama penanggung jawab akun');
            $table->string('email')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('password');
            $table->boolean('is_active')->default(true);
            $table->boolean('password_changed')->default(false);
            $table->timestamp('last_login')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('madrasah_users');
    }
};
