<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\I18n\Time;

class Bos extends BaseController
{
    protected $userModel;
    protected $penggajian;
    protected $presensiModel;
    public function __construct()
    {
     $this->userModel = new \App\Models\User();
     $this->penggajian = new \App\Controllers\Penggajian();
     $this->presensiModel = new \App\Models\presensiModel();
    }
   
    public function index()
    {
     if (session('jabatan') != 'bos')
      return redirect()->to('/dashboard');
   
     $totalUser = $this->userModel->countAll();
   
     $penggajian = $this->penggajian->hitungGaji();
   
     $absensi = $this->presensiModel->where('tanggal_presensi', date('Y-m-d'))->findAll();
   
     foreach ($penggajian as $p) {
      foreach ($absensi as $a) {
       if ($p['id_pegawai'] == $a['id_pegawai']) {
        $penggajian[$p['id_pegawai']]['info'] = $a['info'];
       } else {
        $penggajian[$p['id_pegawai']]['info'] = 'belum absen';
       }
      }
     }
     // dd($absensi);
   
     $data = [
      'title' => 'Dashboard',
      'totalUser' => $totalUser,
      'penggajian' => $penggajian,
     ];
     // bos/index itu laman dashboard
     return view('bos/index', $data);
    }
   
    public function tampil()
    {
     if (session('jabatan') != 'bos')
      return redirect()->to('/dashboard');
    $users = $this->userModel->findAll();
    // foreach( $users as $u){
    //     $kocak =  Time::parse($u['tanggal_masuk_kerja']);
    //     $u['kocak'] = $kocak->humanize();
    // }
    // dd($users);
     $data = [
      'title' => 'BOS',
      // Menampilkan daftar user
      'users' => $users
     ];
     return view('bos/tampil', $data);
    }
   
    public function store()
    {
     if (session('jabatan') != 'bos')
      return redirect()->to('/dashboard');
     $id = $this->request->getVar('id-user');
     $session = session();
     if ($id == '') { //ini berarti tambah data
   
      // Mengambil data dari form input
    //   dd($this->request->getVar('tanggal_masuk_kerja'));
      $data = [
       'username' => $this->request->getVar('username'),
       'password' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT),
       'jabatan' => $this->request->getVar('jabatan'),
       'gaji' => $this->request->getVar('gaji'),
       'tanggal_masuk_kerja' => $this->request->getVar('tanggal_masuk_kerja')
      ];
   
      // Memasukkan data ke dalam database
      $this->userModel->insert($data);
   
      $session->set('alert', 'success');
     } else { //ini berarti edit data
      $this->update($id);
      $session->set('alert', 'edit');
     }
     // Mengarahkan pengguna kembali ke halaman daftar user
     return redirect()->to('/bos/tampil');
    }
   
    public function update($id)
    {
     if (session('jabatan') != 'bos')
      return redirect()->to('/dashboard');
     // Mengambil data dari form input
     $data = [
      'username' => $this->request->getVar('username'),
      'password' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT),
      'jabatan' => $this->request->getVar('jabatan'),
      'gaji' => $this->request->getVar('gaji')
     ];
     if ($this->request->getVar('password') == '') {
      unset($data['password']);
     }
   
     // Memperbarui data ke dalam database
     $this->userModel->update($id, $data);
    }
}
