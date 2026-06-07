<?php

namespace Database\Seeders;

use App\Models\DocumentReminder;
use App\Models\DocumentType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DocumentReminderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::whereIn('role', [User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN])->first();
        $user = User::where('role', User::ROLE_USER)->first();

        if (! $admin || ! $user) {
            return;
        }

        $makeRecord = function (array $data): array {
            return array_merge([
                'pic_nama' => 'Irfaan',
                'pic_telpon' => '087712733183',
            ], $data);
        };

        $sertifikatTypeId = DocumentType::query()->where('nama_jenis', 'sertifikat')->value('id');
        $wajibLaporTypeId = DocumentType::query()->where('nama_jenis', 'wajib lapor tahunan')->value('id');

        $records = [
            $makeRecord([
                'user_id' => $admin->id,
                'nama_dokumen' => 'Sertifikat Laik Operasi Genset',
                'no_dokumen' => 'SLO-001',
                'jenis_dokumen' => $sertifikatTypeId,
                'penerbit_tujuan' => 'PT Global Sertifikasi Sejahtera',
                'tanggal_terbit' => Carbon::create(2023, 7, 1)->toDateString(),
                'tanggal_expired' => Carbon::create(2026, 6, 30)->toDateString(),
                'reminder_bulan' => 3,
                'pic_nama' => 'Irfaan Naufal',
                'pic_telpon' => '087712733183',
                'pic_external_nama' => 'Mutiara',
                'pic_external_telpon' => '081212121212',
                'attachment_path' => 'document-reminders/sample-slo-genset.png',
                'attachment_name' => 'sample-slo-genset.png',
            ]),
            $makeRecord([
                'user_id' => $user->id,
                'nama_dokumen' => 'Sertifikat ISO 9001',
                'no_dokumen' => 'ISO-002',
                'jenis_dokumen' => $sertifikatTypeId,
                'penerbit_tujuan' => 'Bureau Veritas',
                'tanggal_terbit' => Carbon::create(2024, 11, 5)->toDateString(),
                'tanggal_expired' => Carbon::create(2026, 6, 5)->toDateString(),
                'reminder_bulan' => 3,
                'pic_nama' => 'Andi Saputra',
                'pic_telpon' => '087712733183',
                'pic_external_nama' => 'Mutiara ',
                'pic_external_telpon' => '081212121212',
                'attachment_path' => 'document-reminders/sample-iso-9001.png',
                'attachment_name' => 'sample-iso-9001.png',
            ]),
            $makeRecord([
                'user_id' => $admin->id,
                'nama_dokumen' => 'Sertifikat K3',
                'no_dokumen' => 'K3-004',
                'jenis_dokumen' => $sertifikatTypeId,
                'penerbit_tujuan' => 'Kementerian Ketenagakerjaan',
                'tanggal_terbit' => Carbon::create(2024, 8, 22)->toDateString(),
                'tanggal_expired' => Carbon::create(2026, 8, 22)->toDateString(),
                'reminder_bulan' => 6,
                'pic_nama' => 'Irfaan Naufal',
                'pic_telpon' => '087712733183',
                'pic_external_nama' => '',
                'pic_external_telpon' => '',
                'attachment_path' => 'document-reminders/sample-k3.png',
                'attachment_name' => 'sample-k3.png',
            ]),
            $makeRecord([
                'user_id' => $admin->id,
                'nama_dokumen' => 'Sertifikat Audit Internal',
                'no_dokumen' => 'AUD-005',
                'jenis_dokumen' => $sertifikatTypeId,
                'penerbit_tujuan' => 'Internal Audit Team',
                'tanggal_terbit' => Carbon::create(2025, 2, 17)->toDateString(),
                'tanggal_expired' => Carbon::create(2026, 10, 17)->toDateString(),
                'reminder_bulan' => 3,
                'pic_nama' => 'Irfaan Naufal',
                'pic_telpon' => '087712733183',
                'pic_external_nama' => '',
                'pic_external_telpon' => '',
                'attachment_path' => 'document-reminders/sample-audit-internal.png',
                'attachment_name' => 'sample-audit-internal.png',
            ]),
            $makeRecord([
                'user_id' => $user->id,
                'nama_dokumen' => 'Wajib Lapor Tahunan Operasional',
                'no_dokumen' => 'WLT-004',
                'jenis_dokumen' => $wajibLaporTypeId,
                'penerbit_tujuan' => 'Instansi Operasional Daerah',
                'tanggal_terbit' => Carbon::create(2025, 3, 1)->toDateString(),
                'tanggal_expired' => Carbon::create(2026, 6, 1)->toDateString(),
                'reminder_bulan' => 1,
                'pic_nama' => 'Dewi Anggraini',
                'pic_telpon' => '087712733183',
                'pic_external_nama' => '',
                'pic_external_telpon' => '',
                'attachment_path' => 'document-reminders/sample-wlt-operasional.pdf',
                'attachment_name' => 'sample-wlt-operasional.pdf',
            ]),
            $makeRecord([
                'user_id' => $user->id,
                'nama_dokumen' => 'Wajib Lapor Tahunan Lingkungan',
                'no_dokumen' => 'WLT-001',
                'jenis_dokumen' => $wajibLaporTypeId,
                'penerbit_tujuan' => 'Dinas Lingkungan Hidup',
                'tanggal_terbit' => Carbon::create(2024, 9, 12)->toDateString(),
                'tanggal_expired' => Carbon::create(2026, 9, 12)->toDateString(),
                'reminder_bulan' => 6,
                'pic_nama' => 'Andi Saputra',
                'pic_telpon' => '087712733183',
                'pic_external_nama' => '',
                'pic_external_telpon' => '',
                'attachment_path' => 'document-reminders/sample-wlt-lingkungan.pdf',
                'attachment_name' => 'sample-wlt-lingkungan.pdf',
            ]),
            $makeRecord([
                'user_id' => $user->id,
                'nama_dokumen' => 'Wajib Lapor Tahunan Pajak',
                'no_dokumen' => 'WLT-003',
                'jenis_dokumen' => $wajibLaporTypeId,
                'penerbit_tujuan' => 'Kantor Pajak Pratama',
                'tanggal_terbit' => Carbon::create(2024, 5, 8)->toDateString(),
                'tanggal_expired' => Carbon::create(2027, 2, 14)->toDateString(),
                'reminder_bulan' => 12,
                'pic_nama' => 'Budi Hartono',
                'pic_telpon' => '087712733183',
                'pic_external_nama' => '',
                'pic_external_telpon' => '',
                'attachment_path' => 'document-reminders/sample-wlt-pajak.pdf',
                'attachment_name' => 'sample-wlt-pajak.pdf',
            ]),
            $makeRecord([
                'user_id' => $user->id,
                'nama_dokumen' => 'Wajib Lapor Tahunan Ketenagakerjaan',
                'no_dokumen' => 'WLT-002',
                'jenis_dokumen' => $wajibLaporTypeId,
                'penerbit_tujuan' => 'Disnaker',
                'tanggal_terbit' => Carbon::create(2024, 1, 20)->toDateString(),
                'tanggal_expired' => Carbon::create(2026, 7, 20)->toDateString(),
                'reminder_bulan' => 6,
                'pic_nama' => 'Sari Wulandari',
                'pic_telpon' => '087712733183',
                'pic_external_nama' => '',
                'pic_external_telpon' => '',
                'attachment_path' => 'document-reminders/sample-wlt-ketenagakerjaan.pdf',
                'attachment_name' => 'sample-wlt-ketenagakerjaan.pdf',
            ]),
            $makeRecord([
                'user_id' => $user->id,
                'nama_dokumen' => 'Wajib Lapor Tahunan Keamanan',
                'no_dokumen' => 'WLT-005',
                'jenis_dokumen' => $wajibLaporTypeId,
                'penerbit_tujuan' => 'Dinas Keamanan Kota',
                'tanggal_terbit' => Carbon::create(2024, 7, 30)->toDateString(),
                'tanggal_expired' => Carbon::create(2026, 12, 30)->toDateString(),
                'reminder_bulan' => 6,
                'pic_nama' => 'Rudi Setiawan',
                'pic_telpon' => '087712733183',
                'pic_external_nama' => '',
                'pic_external_telpon' => '',
                'attachment_path' => 'document-reminders/sample-wlt-keamanan.pdf',
                'attachment_name' => 'sample-wlt-keamanan.pdf',
            ]),
            // $makeRecord([
            //     'user_id' => $user->id,
            //     'nama_dokumen' => 'Wajib Lapor Tahunan Lingkungan Utama',
            //     'no_dokumen' => 'WLT-006',
            //     'jenis_dokumen' => $wajibLaporTypeId,
            //     'penerbit_tujuan' => 'Dinas Lingkungan Hidup',
            //     'tanggal_terbit' => Carbon::create(2025, 1, 25)->toDateString(),
            //     'tanggal_expired' => Carbon::create(2026, 4, 25)->toDateString(),
            //     'reminder_bulan' => 1,
            //     'pic_nama' => 'Nina Kartika',
            //     'pic_telpon' => '087712733183',
            //     'attachment_path' => 'document-reminders/sample-wlt-lingkungan-utama.pdf',
            //     'attachment_name' => 'sample-wlt-lingkungan-utama.pdf',
            // ]),
            // $makeRecord([
            //     'user_id' => $user->id,
            //     'nama_dokumen' => 'SLO Genset',
            //     'no_dokumen' => 'SLO-009',
            //     'jenis_dokumen' => $wajibLaporTypeId,
            //     'penerbit_tujuan' => 'Sertifikasi Genset Indonesia',
            //     'tanggal_terbit' => Carbon::create(2022, 7, 30)->toDateString(),
            //     'tanggal_expired' => Carbon::create(2026, 6, 12)->toDateString(),
            //     'reminder_bulan' => 6,
            //     'pic_nama' => 'Daehoon Lee',
            //     'pic_telpon' => '087712733183',
            //     'attachment_path' => 'document-reminders/sample-wlt-keamanan.pdf',
            //     'attachment_name' => 'sample-wlt-keamanan.pdf',
            // ]),
            $makeRecord([
                'user_id' => $user->id,
                'nama_dokumen' => 'Badan Nasional Sertifikasi Profesi',
                'no_dokumen' => 'BNSP-006',
                'jenis_dokumen' => $sertifikatTypeId,
                'penerbit_tujuan' => 'Junior Engineer Certification',
                'tanggal_terbit' => Carbon::create(2025, 1, 25)->toDateString(),
                'tanggal_expired' => Carbon::create(2026, 7, 1)->toDateString(),
                'reminder_bulan' => 1,
                'pic_nama' => 'Rafdean Pratama',
                'pic_telpon' => '087712733183',
                'pic_external_nama' => '',
                'pic_external_telpon' => '',
                'attachment_path' => 'document-reminders/sample-wlt-lingkungan-utama.pdf',
                'attachment_name' => 'sample-wlt-lingkungan-utama.pdf',
            ]),
        ];

        foreach ($records as $record) {
            DocumentReminder::updateOrCreate(
                ['no_dokumen' => $record['no_dokumen']],
                $record
            );
        }
    }
}
