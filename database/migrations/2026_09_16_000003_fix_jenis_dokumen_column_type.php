<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::table('document_reminders')
                ->join('document_types', 'document_types.nama_jenis', '=', 'document_reminders.jenis_dokumen')
                ->whereRaw('document_reminders.jenis_dokumen NOT REGEXP ?', ['^[0-9]+$'])
                ->update(['document_reminders.jenis_dokumen' => DB::raw('document_types.id')]);

            DB::table('document_reminders')
                ->whereRaw('jenis_dokumen NOT REGEXP ?', ['^[0-9]+$'])
                ->update(['jenis_dokumen' => null]);
        } else {
            $typeMap = DB::table('document_types')->pluck('id', 'nama_jenis');

            DB::table('document_reminders')
                ->whereNotNull('jenis_dokumen')
                ->orderBy('id')
                ->get()
                ->each(function ($row) use ($typeMap) {
                    $jenis = (string) $row->jenis_dokumen;
                    if ($jenis !== '' && ! ctype_digit($jenis)) {
                        $mapped = $typeMap[$jenis] ?? null;
                        DB::table('document_reminders')
                            ->where('id', $row->id)
                            ->update(['jenis_dokumen' => $mapped]);
                    } elseif ($jenis !== '' && ctype_digit($jenis)) {
                        // already numeric id — keep
                    } elseif ($jenis === '') {
                        DB::table('document_reminders')
                            ->where('id', $row->id)
                            ->update(['jenis_dokumen' => null]);
                    }
                });
        }

        Schema::table('document_reminders', function (Blueprint $table) use ($driver) {
            $table->unsignedBigInteger('jenis_dokumen')->nullable()->change();
            if ($driver === 'mysql') {
                $table->foreign('jenis_dokumen')->references('id')->on('document_types')->nullOnDelete();
            }
            $table->index('jenis_dokumen');
        });
    }

    public function down(): void
    {
        Schema::table('document_reminders', function (Blueprint $table) {
            $table->dropForeign(['jenis_dokumen']);
            $table->dropIndex(['jenis_dokumen']);
            $table->string('jenis_dokumen')->nullable()->change();
        });
    }
};
