<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;
use DateInterval;
use DateTime;

class GajiSeeder extends Seeder
{
    protected $pegawaiModel;

    public function __construct()
    {
        $this->pegawaiModel = new \App\Models\User();
    }
    public function run()
    {
        $db = \Config\Database::connect();
        $faker = \Faker\Factory::create();
        $this->db->table('penggajian')->truncate();

        // Retrieve all users (karyawan)
        $users = $db->table('user')->select('id_user, jabatan, tanggal_masuk_kerja, gaji')->get()->getResultArray();

        foreach ($users as $user) {
            if ($user['jabatan'] == 'bos') {
                continue;
            }
            $startDate = new DateTime($user['tanggal_masuk_kerja']);
            $awalMasuk = 1;
            $currentDate = new DateTime(); // Current date (end of the month)
            $currentDate->modify('last day of this month'); // Set to the last day of the current month

            // Get the initial salary and set it to the starting point
            $salary = $user['gaji'];

            // Loop through each month starting from the user’s hire date
            while ($startDate <= $currentDate) {
                // Format the current salary record for the month
                $salaryRecord = $this->hitungGaji($startDate->format('m'));

                // Insert the salary record into the gaji table
                $db->table('penggajian')->insert($salaryRecord);

                // Every 3 months, increase salary by 300,000, up to a max of 2,300,000
                if ($awalMasuk % 3 == 0 && $salary < 2300000) {
                    $salary += 200000;
                    if ($salary > 2300000) {
                        $salary = 2300000; // Cap the salary at 2,300,000
                    }
                }

                // Move to the next month
                $startDate->add(new DateInterval('P1M'));
            }
        }
    }
    public function hitungGaji($namaBulan)
    {
        

        // ambil data nama karyawan dari tabel user join dengan tabel presensi group by nama count jam masuk
        $pegawai = $this->pegawaiModel->join('presensi', 'presensi.id_pegawai = user.id_user')->where('month(presensi.tanggal_presensi)', $namaBulan)->findAll();
        // mengubah data array dan menghitung jumlah jam masuk

        $penggajian = [];

        foreach ($pegawai as $p) {
            $jamMasuk = Time::parse($p['waktu_masuk']);
            $jamKeluar = Time::parse($p['waktu_keluar']);
            $jamKerjaHarian = $jamMasuk->difference($jamKeluar)->getHours();
            ($p['ket'] == 'terlambat') ? $terlambat = 1 : $terlambat = 0;
            ($p['info'] == 'sakit') ? $sakit = 1 : $sakit = 0;

            $hariMasuk = $this->jamKerja()['kerja_sampai_hari_ini'];
            $totalHariKerja = $this->jamKerja()['hari_kerja_sebulan'];

            // masukkan data ke array baru jika nama berbeda
            if (!array_key_exists($p['id_pegawai'], $penggajian)) {

                $penggajian[$p['id_pegawai']] = [
                    'id_pegawai' => $p['id_pegawai'],
                    'nama' => $p['username'] . '(' . $p['jabatan'] . ')',
                    'gaji' => $p['gaji'],
                    'jam_kerja' => $jamKerjaHarian,
                    'telat' => $terlambat,
                    'sakit' => $sakit,
                    'masuk' => $hariMasuk - $sakit,
                ];
            } else {
                $penggajian[$p['id_pegawai']]['jam_kerja'] += $jamKerjaHarian;
                $penggajian[$p['id_pegawai']]['telat'] += $terlambat;
                $penggajian[$p['id_pegawai']]['sakit'] += $sakit;
                $penggajian[$p['id_pegawai']]['masuk'] = $hariMasuk - $sakit;
            }
        }
        // foreach lagi untuk menambah gaji sekarang
        foreach ($penggajian as $p) {
            $penggajian[$p['id_pegawai']]['gaji_sekarang'] =  - ($p['telat'] * 10000) + ($p['masuk'] * $p['gaji'] / $totalHariKerja);
        }

        return $penggajian;
    }

    public function jamKerja()
    {
        $hariIni = new Time('now', 'Asia/Jakarta', 'id_id');
        $namaBulan = $hariIni->format('m');

        // hitung hari dalam bulan kecuali hari jumat
        $hariKerjaSebulan = 0;
        $MasukSampaiHariIni = 0;

        for ($i = 1; $i <= $hariIni->format('t'); $i++) {
            $hari = new Time($hariIni->format('Y') . '-' . $namaBulan . '-' . $i, 'Asia/Jakarta', 'id_id');
            if ($hari->format('D') != 'Fri') {
                if ($hari <= $hariIni) {
                    $MasukSampaiHariIni++;
                }
                $hariKerjaSebulan++;
            }
        }

        $value = [
            'hari_kerja_sebulan' => $hariKerjaSebulan,
            'kerja_sampai_hari_ini' => $MasukSampaiHariIni,
        ];
        return $value;
    }
}
