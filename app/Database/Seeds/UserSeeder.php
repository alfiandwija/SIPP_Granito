<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        // bos, admin, karyawan
        $data = [
            [
                'username' => 'Ghoni',
                'password' => password_hash('Ghoni', PASSWORD_DEFAULT),
                'jabatan'   => 'bos',
                'tanggal_masuk_kerja'=>'2015-07-09',
                'gaji'   => ''
            ],
            [
                'username' => 'ian',
                'password' => password_hash('ian', PASSWORD_DEFAULT),
                'jabatan'   => 'karyawan',
                'tanggal_masuk_kerja'=>'2021-12-06',
                'gaji'   => '2300000'
            ],
            [
                'username' => 'riqqi',
                'password' => password_hash('riqqi', PASSWORD_DEFAULT),
                'jabatan'   => 'karyawan',
                'tanggal_masuk_kerja'=>'2024-01-06',
                'gaji'   => '1300000'
            ],
        ];

        // Using Query Builder
        $this->db->table('user')->insertBatch($data);

    }
}
