<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('document_types', function (Blueprint $table) {
            $table->id();
            $table->string('nama_jenis')->unique();
            $table->string('label');
            $table->string('tipe_form')->nullable();
            $table->timestamps();
        });

        DB::table('document_types')->insert([
            ['nama_jenis' => 'Sertifikat', 'label' => 'Sertifikat', 'tipe_form' => 'sertifikat', 'created_at' => now(), 'updated_at' => now()],
            ['nama_jenis' => 'Wajib Lapor Tahunan', 'label' => 'Wajib Lapor Tahunan', 'tipe_form' => 'wajib_lapor_tahunan', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_types');
    }
};