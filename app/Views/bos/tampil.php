<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<?= $disabled = '' ?>
<div class="row">
 <?php
use CodeIgniter\I18n\Time;

use CodeIgniter\CodeIgniter;

 if (session()->get('alert') == 'success') : ?>
  <div class="alert alert-success alert-dismissible fade show" role="alert">
   <i class="fa-regular fa-badge-check fa-bounce"></i>
   Data user berhasil ditambahkan!
   <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
 <?php elseif (session()->get('alert') == 'edit') : ?>
  <div class="alert alert-primary alert-dismissible fade show" role="alert">
   <i class="fa-regular fa-badge-check fa-bounce"></i>
   Data user berhasil di Update!
   <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
  <?php session()->remove('alert') ?>
 <?php endif ?>


 <div class="col-sm-8">
  <h1 class="mt-2">Daftar User</h1>

  <table class="table table-bordered table-hover">
   <thead>
    <tr>
     <th scope="col">NO</th>
     <th scope="col">Username</th>
     <th scope="col">Jabatan</th>
     <th scope="col">Gaji</th>
     <th scope="col">Tanggal Masuk Kerja</th>
     <th scope="col">Aksi</th>
    </tr>
   </thead>
   <tbody>
    <?php $no = 1; ?>
    <?php foreach ($users as $user) : ?>
     <tr>
      <th scope="row">
       <?= $no++; ?>
      </th>
      <td>
       <?= $user['username']; ?>
      </td>
      <td>
       <?= $user['jabatan']; ?>
      </td>
      <td>
       <?= "Rp " . number_format($user['gaji'], 0, ',', '.'); ?>
      </td>
      <td>
<?php 
$time = Time::parse($user['tanggal_masuk_kerja']);
?>
       <?= $user['tanggal_masuk_kerja']; ?> (<?= $time->humanize()?>)
      </td>
      <?php ($user['jabatan'] == 'bos') ? $disabled = 'disabled' : $disabled = '' ?>
      <td>
       <a class="btn btn-warning btn-sm <?= $disabled; ?>" onclick="editData(<?= $user['id_user']; ?>,`<?= $user['username']; ?>`,`<?= $user['jabatan']; ?>`,`<?= $user['gaji']; ?>`,`<?= $user['tanggal_masuk_kerja']; ?>`)" id="btn-edit">Edit</a>
      </td>
     </tr>
    <?php endforeach; ?>
   </tbody>
  </table>
 </div>
 <div class="col-md-4">
  <button class="btn btn-warning my-2 position-relative" id="btn-tambah">Tambah User</button>
  <div class="card mt-3" id="card-form" style="display: none;">
   <div class="card-body">
    <h5 class="card-title" id="card-title">Tambah Data</h5>

    <form action="<?= base_url('/bos/store'); ?>" method="post">
     <input type="hidden" id="id-user" name="id-user" value="">
     <div class="mb-3">
      <label for="username" class="form-label">Username</label>
      <input type="text" class="form-control" id="username" name="username" required value="">
     </div>
     <div class="mb-3">
      <label for="password" class="form-label" id="lbl-pass">Password</label>
      <input type="password" class="form-control" name="password" required value="" id="password">
     </div>
     <div class="mb-3">
      <label for="jabatan" class="form-label">Jabatan</label>
      <select class="form-select" id="jabatan" name="jabatan" required>
       <option value="">-- Pilih Jabatan --</option>
       <option value="karyawan">Karyawan</option>
       <option value="bos">Bos</option>
      </select>
     </div>
     <div class="mb-3">
      <label for="gaji" class="form-label">Gaji</label>
      <input type="number" class="form-control" id="gaji" name="gaji" readonly value="1300000 ">
     </div>
     <div class="mb-3">
      <label for="tanggal_masuk_kerja" class="form-label">Tanggal Masuk Kerja</label>
      <input type="date" class="form-control" id="tanggal_masuk_kerja" name="tanggal_masuk_kerja" readonly value="<?= date('Y-m-d'); ?>">
     </div>
     <button type="submit" class="btn btn-warning" id="btn-form">Simpan</button>
     <button type="button" class="btn btn-secondary" id="btn-batal">Batal</button>
    </form>
   </div>
  </div>
 </div>
</div>


<script>
 const btnTambah = document.querySelector('#btn-tambah');
 const cardForm = document.querySelector('#card-form');
 const btnBatal = document.querySelector('#btn-batal');
 const cardTitle = document.querySelector('#card-title');
 const elId = document.querySelector('#id-user');
 const elUsername = document.querySelector('#username');
 const elPass = document.querySelector('#password');
 const lblPass = document.querySelector('#lbl-pass');
 const elJabatan = document.querySelector('#jabatan');
 const elGaji = document.querySelector('#gaji');
 const elTanggalMasuk = document.querySelector('#tanggal_masuk_kerja');
 const btnForm = document.querySelector('#btn-form');

 btnTambah.addEventListener('click', function() {
  cardForm.style.display = 'block';
  elTanggalMasuk.value = '<?= date('Y-m-d'); ?>';
  elGaji.value = 1300000; // Set default gaji to Rp 1,300,000
 });

 btnBatal.addEventListener('click', function() {
  btnTambah.style.display = 'block';

  cardTitle.innerHTML = 'Tambah Data';
  elId.value = '';
  elUsername.value = '';
  elPass.setAttribute('placeholder', "Masukkan Password");
  elPass.setAttribute('required', true);
  lblPass.innerHTML = 'Password';
  elJabatan.value = '';
  elGaji.value = 1300000; // Reset default gaji to Rp 1,300,000
  elTanggalMasuk.value = '<?= date('Y-m-d'); ?>';
  btnForm.innerHTML = 'Simpan';

  cardForm.style.display = 'none';
 });

 function editData(id, username, jabatan, gaji, tanggalMasuk) {
  cardForm.style.display = 'block';
  btnTambah.style.display = 'none';

  cardTitle.innerHTML = 'Edit Data';
  elId.value = id;
  elUsername.value = username;
  elPass.setAttribute('placeholder', "biarkan kosong jika tidak ingin diubah");
  elPass.removeAttribute('required');
  lblPass.innerHTML = 'Password Baru';
  elJabatan.value = jabatan;
  elGaji.value = gaji;
  elTanggalMasuk.value = tanggalMasuk;
  btnForm.innerHTML = 'Update';

  elGaji.setAttribute('readonly', true);
 }
</script>
<?= $this->endSection(); ?>
