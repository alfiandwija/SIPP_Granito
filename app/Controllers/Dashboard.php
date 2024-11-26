<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Dashboard extends BaseController
{
    public function index()
    {
        //cek dulu jabatannya
        // bos, karyawan, admin
        $session = session();
        $jabatan = $session->get('jabatan');
        if ($jabatan == 'bos') {
            return redirect()->to('bos');
        } else if ($jabatan == 'karyawan') {
            return redirect()->to('karyawan');
        } else {
            return redirect()->to('home');
        }
    }
}
