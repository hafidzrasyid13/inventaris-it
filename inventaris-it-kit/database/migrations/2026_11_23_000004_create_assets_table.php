<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('kode_aset', 30)->unique();
            $table->string('nama', 150);
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->foreignId('location_id')->constrained('locations')->restrictOnDelete();
            $table->string('merk', 100)->nullable();
            $table->string('serial_number', 100)->nullable()->unique();
            $table->ipAddress('ip_address')->nullable();
            $table->date('tanggal_beli')->nullable();
            $table->enum('status', ['aktif', 'maintenance', 'rusak', 'nonaktif'])->default('aktif');
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
