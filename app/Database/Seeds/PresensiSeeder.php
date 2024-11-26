<?php

namespace App\Database\Seeds;

use App\Models\User;
use CodeIgniter\Database\Seeder;

use CodeIgniter\I18n\Time;
use DateInterval;
use DateTime;

class PresensiSeeder extends Seeder
{
    public function run()
    {
        // truncate data di tabel presensi

        $this->db->table('presensi')->truncate();
        $allUser = $this->db->table('user')->where('jabatan','karyawan')->get()->getResultArray();
        $faker = \Faker\Factory::create('id_ID');
        $awalBulan = date('Y-m-01');
        $hariIni = date('d');
        // buat perulangan untuk memasukkan faker data ke tabel presensi
        // foreach semua user
        foreach ($allUser as $user) {
            $startDate = new DateTime($user['tanggal_masuk_kerja']);
            $currentDate = new DateTime();
            $currentDate->modify('-1 day');
            $nationalHolidays = $this->getNationalHolidays($startDate->format('Y'));
            // ambil data awal user masuk
            while ($startDate <= $currentDate) {
                $dateString = $startDate->format('Y-m-d');
                if ($startDate->format('N') == 5 || in_array($dateString, $nationalHolidays)) {
                    // Skip if it's Friday (5) or a national holiday
                    $startDate->add(new DateInterval('P1D'));
                    continue;
                }

                // Generate random check-in and check-out times
                $info = '';
                $waktuMasuk = $faker->dateTimeBetween('07:45:00', '08:30:00')->format('H:i:s');

                $waktuKeluar = '';
                $ket = '';
                $gambarKeluar = '';
                $num = $faker->randomNumber(1);
                if ($num == 1) {
                    $info = 'sakit';
                    $ket = 'maaf lagi sakit';
                } else {
                    $info = 'pulang';
                    $gambarKeluar = 'default.jpg';
                    $waktuKeluar = $faker->time('16:i:s');
                    // bisa saja maksudnya terlambat
                    $timeMasuk = Time::parse($waktuMasuk);
                    $timeKeluar = Time::parse($waktuKeluar);
                    $thresholdTime = '08:15:00';
                    // Check if the generated time is later than the threshold
                    $ket = (strtotime($waktuMasuk) > strtotime($thresholdTime)) ? 'Terlambat' : 'Hadir';
                }
                $data = [
                    'id_pegawai' => $user['id_user'],
                    // input tanggal setiap hari bulan juni 2023
                    'tanggal_presensi' => $startDate->format('Y-m-d'),
                    // buat waktu masuk between 07:00:00 - 08:00:00
                    'waktu_masuk' => $waktuMasuk,
                    'waktu_keluar' => $waktuKeluar,
                    'gambar_masuk' => 'default.jpg',
                    'gambar_keluar' => $gambarKeluar,
                    'info' => $info, //[masuk/terlambat, pulang], sakit, 
                    'ket' => $ket, // terlambat, ket sakit
                    'status' => 1,
                ];
                // insert data ke database
                $this->db->table('presensi')->insert($data);
                // Move to the next day
                $startDate->add(new DateInterval('P1D'));
            }
        }
    }
    private function getNationalHolidays($startYear)
    {
        $currentYear = date('Y');
        $holidays = [];

        // Fetch holidays for each year from the start year to the current year
        for ($year = $startYear; $year <= $currentYear; $year++) {
            $url = "https://dayoffapi.vercel.app/api?year=" . $year;

            // Fetch the data using file_get_contents or cURL
            $response = file_get_contents($url);
            if ($response === FALSE) {
                die('Error occurred while fetching national holidays for year ' . $year);
            }

            // Decode the JSON response
            $data = json_decode($response, true);

            // Merge the holidays of the current year
            if (isset($data['holidays'])) {
                $holidays = array_merge($holidays, $data['holidays']);
            }
        }

        // Return an array of holiday dates
        return $holidays;
    }
}
