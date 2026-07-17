<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengajuan', function (Blueprint $table) {
            // Nullable: pengajuan dari form publik tetap null,
            // pengajuan dari dashboard guru otomatis terisi.
            $table->foreignId('guru_user_id')
                ->nullable()
                ->after('id')
                ->constrained('guru_users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan', function (Blueprint $table) {
            $table->dropConstrainedForeignId('guru_user_id');
        });
    }
};
