<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_tiket', 20)->unique();
            $table->foreignId('asset_id')->constrained('assets')->restrictOnDelete();
            $table->foreignId('pelapor_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('teknisi_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('judul', 150);
            $table->text('deskripsi');
            $table->enum('prioritas', ['rendah', 'sedang', 'tinggi'])->default('sedang');
            $table->enum('status', ['baru', 'diproses', 'selesai', 'ditutup'])->default('baru');
            $table->timestamp('selesai_pada')->nullable();
            $table->timestamps();

            $table->index(['status', 'prioritas']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
