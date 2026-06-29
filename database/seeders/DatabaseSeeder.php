<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Layanan;
use App\Models\Lead;
use App\Models\PengechekanLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Users
        $admin = User::create([
            'name' => 'Admin System',
            'email' => 'admin@esdea.com',
            'password' => Hash::make('password'),
            'role' => 'ADMIN',
            'user_id' => '100001',
        ]);

        $leader = User::create([
            'name' => 'Budi Leader',
            'email' => 'leader@esdea.com',
            'password' => Hash::make('password'),
            'role' => 'LEADER',
            'user_id' => '200001',
        ]);

        $sales1 = User::create([
            'name' => 'Andi Sales',
            'email' => 'sales1@esdea.com',
            'password' => Hash::make('password'),
            'role' => 'SALES',
            'leader_id' => $leader->id,
            'user_id' => '300001',
        ]);
        
        $sso = User::create([
            'name' => 'Caca SSO',
            'email' => 'sso@esdea.com',
            'password' => Hash::make('password'),
            'role' => 'SSO',
            'user_id' => '400001',
        ]);

        // 2. Create Layanan (Rule: Sertifikasi ISO)
        $layanan1 = Layanan::create([
            'nama_layanan' => 'Sertifikasi ISO 9001',
            'harga_modal' => 10000000,
            'harga_pokok' => 12000000,
            'komisi_sales' => 500000,
            'komisi_sso' => 50000,
        ]);
        
        $layanan2 = Layanan::create([
            'nama_layanan' => 'Sertifikasi ISO 14001',
            'harga_modal' => 15000000,
            'harga_pokok' => 18000000,
            'komisi_sales' => 750000,
            'komisi_sso' => 50000,
        ]);

        // 3. Create Leads (Rule: PT ... ABADI)
        $lead1 = Lead::create([
            'sales_id' => $sales1->id,
            'sso_id' => $sso->id,
            'status_leads' => 'NEW',
            'kualifikasi' => 'HOT',
            'nama_perusahaan' => 'PT. Maju ABADI',
            'jenis_perusahaan' => 'Konstruksi',
            'tingkat_kualifikasi' => 'K/M/B',
            'sub_klasifikasi' => 'Sipil',
            'tanggal_expired' => '2027-01-01',
            'nama_pic' => 'Bapak Joko',
            'alamat' => 'Jl. Sudirman No. 123, Jakarta',
            'no_telepon' => '081234567890',
            'email' => 'joko@majuabadi.com',
            'wilayah' => ['Jakarta', 'DKI Jakarta'],
        ]);

        // 4. Create Pengechekan Logs (Rule: pengechekan instead of pelacakan)
        PengechekanLog::create([
            'lead_id' => $lead1->id,
            'user_id' => $sales1->id,
            'catatan_pengechekan' => 'Melakukan pengechekan dokumen legalitas PT. Maju ABADI, semua valid.',
        ]);
        
        PengechekanLog::create([
            'lead_id' => $lead1->id,
            'user_id' => $sso->id,
            'catatan_pengechekan' => 'Pengechekan awal oleh SSO, data prospek sangat bagus.',
        ]);
    }
}
