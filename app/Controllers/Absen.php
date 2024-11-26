<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\I18n\Time;
use CodeIgniter\HTTP\ResponseInterface;

class Absen extends BaseController
{
    protected $presensiModel;

    public function __construct()
    {
        $this->presensiModel = new \App\Models\presensiModel();
    }

    public function index()
    {

        if (session()->get('jabatan') == 'bos') return redirect()->to('/dashboard');

        $absen = $this->presensiModel->where('id_pegawai', session()->get('id'))->findAll();

        $time = new Time('now', 'Asia/Jakarta', 'id_id');
        $tanggalHariIni = $time->toLocalizedString('yyyy-MM-dd');

        $info = '';
        // cari data hari ini di array absen untuk info absen
        $hariIni = $this->presensiModel->where('id_pegawai', session()->get('id'))->where('tanggal_presensi', $tanggalHariIni)->first();
        // kondisi ketika masuk,pulang,sakit, sudah absen
        if ($hariIni == null) {
            // jika data hari ini tidak ada = masuk/sakit
            $info = 'masuk';
        } else {
            if ($hariIni['info'] == 'masuk') {
                // jika data hari ini ada dan sudah absen masuk = absen pulang
                $info = 'pulang';
            } elseif ($hariIni['info'] == 'pulang') {
                // jika data hari ini ada dan sudah absen pulang = sudah pulang
                $info = 'sudah pulang';
            } elseif ($hariIni['info'] == 'sakit') {
                // jika data hari ini ada dan sudah absen sakit = tidak bisa absen pulang
                $info = 'sakit';
            }
        }

        $data = [
            'title' => 'Absensi',
            'info'  => $info,
            'absen' => $absen
        ];
        return view('absen', $data);
    }

    public function presensi()
    {
        if (session()->get('jabatan') == 'bos') return redirect()->to('/dashboard');

        $imageData = $this->request->getVar('imageData');
        $info = $this->request->getPost('info');
        $ket = $this->request->getPost('ket');
        $imageName = time() . '.jpg';
        $imagePath = WRITEPATH . 'uploads/' . $imageName;
        file_put_contents($imagePath, base64_decode($imageData));

        $time = new Time('now', 'Asia/Jakarta', 'id_id');
        $formattedDate = $time->toLocalizedString('EEEE, d MMMM yyyy HH:mm:ss');
        $tanggalPresensi = $time->toLocalizedString('yyyy-MM-dd');
        $waktu = $time->toLocalizedString('HH:mm:ss');

        if ($info == 'masuk' || $info == 'sakit') {
            if ($time->getHour() >= 8 && $time->getMinute() >= 10 && $info == 'masuk') {
                $ket = 'terlambat';
            }

            $data = [
                'id_pegawai' => session()->get('id'),
                'tanggal_presensi' => $tanggalPresensi,
                'waktu_masuk' => $waktu,
                'waktu_keluar' => '00:00:00',
                'gambar_masuk' => $imageName,
                'gambar_keluar' => '',
                'info' => $info,
                'ket' => $ket,
                'status' => 1,
            ];
            $this->presensiModel->insert($data);
        } elseif ($info == 'pulang') {
            // Memeriksa apakah jam sudah lewat 17:00
            if ($time->getHour() < 16) {
                // Berikan respons untuk alert jika belum jam 4 sore
                $response = [
                    'status' => 'error',
                    'message' => 'Tidak bisa absen pulang sebelum jam 4 sore.',
                ];
                return $this->response->setJSON($response);
            }

            $idPresensi = $this->presensiModel->where('id_pegawai', session()->get('id'))->orderBy('id_presensi', 'DESC')->first();
            $ket = $idPresensi['ket'];
            $waktuMasuk = Time::parse($idPresensi['waktu_masuk']);
            $waktuKeluar = Time::parse($waktu);
            if ($waktuMasuk->diff($waktuKeluar)->h == 8) {
                $ket = '';
            }

            $data = [
                'waktu_keluar' => $waktu,
                'gambar_keluar' => $imageName,
                'info' => $info,
                'ket' => $ket,
            ];
            $this->presensiModel->update($idPresensi, $data);
        }

        $response = [
            'status' => 'success',
            'message' => 'Berhasil Absen ' . $info . ' pada ' . $formattedDate,
        ];
        return $this->response->setJSON($response);
    }


    public function getAbsen($id = 0)
    {
        if (session()->get('jabatan') == 'bos') return redirect()->to('/dashboard');

        if ($id == 0) {
            $absen = $this->presensiModel->where('id_pegawai', session()->get('id'))->findAll();
        } else {
            $absen = $this->presensiModel->where('id_pegawai', $id)->findAll();
        }

        $terlambat = 0;
        $sakit = 0;
        $bulan = '';
        // ganti format array menjadi json
        foreach ($absen as $a) {
            // absen masuk
            // dd($a);

            if ($bulan != Time::parse($a['tanggal_presensi'])->getMonth()) {
                $terlambat = 0;
                $sakit = 0;
                $bulan = Time::parse($a['tanggal_presensi'])->getMonth();
            }

            if ($a['info'] != 'sakit') {
                $warna = 'rgb(3, 201, 136)';
                if ($a['ket'] == 'Terlambat') :
                    $warna = 'rgb(237, 43, 42)';
                    $terlambat++;
                else : $a['ket'] = "masuk";
                endif;
                $response[] = [
                    'title' => $a['ket'],
                    'start' => $a['tanggal_presensi'] . 'T' . $a['waktu_masuk'],
                    'color' => $warna,
                    'description' => $a['waktu_masuk'],
                    'gambar' =>   $a['gambar_masuk'],
                    'sakit' => $sakit,
                    'terlambat' => $terlambat,

                ];
                // absen pulang
                if ($a['info'] == 'pulang') {
                    $a['ket'] = 'pulang';
                    $warna = 'rgb(229, 124, 35)';
                    $response[] = [
                        'title' => $a['ket'],
                        'start' => $a['tanggal_presensi'] . 'T' . $a['waktu_keluar'],
                        'color' => $warna,
                        'textColor' => $warna,
                        'description' => $a['waktu_keluar'],
                        'gambar' => $a['gambar_keluar'],
                        'sakit' => $sakit,
                        'terlambat' => $terlambat
                    ];
                }
            } else {
                // absen sakit
                $sakit++;
                $response[] = [
                    'title' =>  $a['info'],
                    'start' => $a['tanggal_presensi'],
                    'color' => 'rgb(255, 237, 0)',
                    'description' => $a['ket'],
                    'gambar' => $a['gambar_masuk'],
                    'sakit' => $sakit,
                    'terlambat' => $terlambat
                ];
            }
        }
        // jumat 
        $awalBulan = date('Y-m-01');
        for ($i = 0; $i < 32; $i++) {
            if (date('D', strtotime($awalBulan . '+' . $i . 'days')) == 'Fri') {
                $response[] = [
                    'title' => 'Libur',
                    'start' =>  date('Y-m-d', strtotime($awalBulan . '+' . $i . 'days')),
                    'backgroundColor' => 'rgb(22, 255, 0)',
                    'display' => 'background',
                    'gambar' => ''
                ];
            }
        }
        return $this->response->setJSON($response);
    }
}
