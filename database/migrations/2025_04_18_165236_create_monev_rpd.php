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
        Schema::create('r_p_d_s', function (Blueprint $table) {
            $table->id();
            $table->string('kegiatan');
            $table->string('jenis_belanja');
            $table->string('output');
            $table->bigInteger('target')->nullable();
            $table->bigInteger('realisasi')->nullable();
            $table->string('pic');
            $table->integer('bulan')->nullable();
            $table->integer('tahun')->nullable();
            $table->string('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('r_p_d_s');
    }
};
