<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<?php if (isset($_GET['id'])) : ?>
  <?php $id = $_GET['id']; ?>
  <?php $nama = $_GET['nama'] ?>
  <script>
    var id = <?= $id ?>;
  </script>
<?php else : ?>
  <?php $nama = session('username') ?>
  <script>
    var id = 0;
  </script>
<?php endif ?>
<!-- <div class="row">
  <p>
    <a class="btn btn-secondary" data-bs-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
      Tata Tertib Penggajian
    </a>
    <a class="btn btn-info" data-bs-toggle="collapse" href="#collapseExample1" role="button" aria-expanded="false" aria-controls="collapseExample1">
      Tata Tertib Absen
    </a>
  </p>
  <p>
  </p>
  <div class="collapse" id="collapseExample">
    <div class="alert alert-secondary" role="alert">
      <ul>
        <li>HRD mengapprove gaji bulanan setiap akhir bulan</li>
        <li>gaji karyawan = dihitung dari total jam masuk karyawan jika masih 8 jam walaupun terlambat maka tidak dikurangi jika kurang dari 8 jam maka dikurangi 10000 perhari</li>
        <li>Bulan Ini = <?= $kerja['hari_kerja_sebulan'] * 8 ?> Jam / <?= $kerja['hari_kerja_sebulan']  ?> Hari </li>
      </ul>
    </div>
  </div>

  <div class="collapse" id="collapseExample1">
    <div class="alert alert-info" role="alert">
      <ul>
        <li>Jam kerja dimulai setiap hari pukul 08.00 - 16.00 WIB kecuali Hari Jumat (libur)</li>
        <li>absen memiliki batas keterlambatan 15 menit</li>
        <li>total jam kerja selama satu minggu adalah 48 jam terhitung 8x6 hari
        </li>
        <li>Setiap pegawai yang mengalami sakit, harus mengajukan surat keterangan sakit lewat rumah</li>
      </ul>
    </div>
  </div>
</div> -->

<!-- tabel -->
<!-- <div class="row">
  <div class="col">

    <h4>Gaji Bulan <span class="bulan-gaji"></span></h4>
    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
      <a class="btn btn-primary" onclick="verify()" <?= $approve ?>>Approve Gaji</a>
    </div>

    <table class="table table-bordered" id="table">
      <thead>
        <tr>
          <th hidden>id pegawai</th>
          <th>Nama</th>
          <th>Gaji Pokok</th>
          <th data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" data-bs-title="full : <?= $kerja['hari_kerja_sebulan'] ?> hari">Hari Masuk</th>
          <th data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" data-bs-title="full : <?= $kerja['hari_kerja_sebulan'] * 8 ?> jam">Total Jam </th>
          <th>terlambat</th>
          <th>sakit</th>
          <th>Gaji Bulan Ini</th>
          <th></th>
        </tr>
      </thead>
      <?php foreach ($penggajian as $p) : ?>
        <tr>
          <td hidden><?= $p['id_pegawai'] ?></td>
          <td><?= $p['nama'] ?></td>
          <td><?= "Rp " . number_format($p['gaji'], 0, ',', '.');  ?></td>
          <td><?= $p['masuk'] ?></td>
          <td><?= $p['hari_kerja'] ?></td>
          <td><?= $p['telat'] ?></td>
          <td><?= $p['sakit'] ?></td>
          <td><?= "Rp" . number_format($p['gaji_sekarang'], 0, ',', '.');  ?></td>
          <td>
            <a href="<?= base_url('penggajian?id=' . $p['id_pegawai']) . '&nama=' . $p['nama'] ?>">detail</a>
          </td>
        </tr>
      <?php endforeach ?>
    </table>
  </div>
</div> -->
<!-- akhir tabel -->

<div class="container-fluid">
  <div class="row">
    <!-- Kolom kiri untuk daftar pengguna -->
    <div class="col-md-3">
      <div class="list-group">
        <?php foreach($penggajian as $p):?>
          <a href="#" class="list-group-item list-group-item-action" id="user1" onclick="showUserDetail(<?= $p['id_pegawai'] ?>)"><?= $p['nama'] ?></a>
        <?php endforeach?>
      </div>
    </div>

    <!-- Kolom kanan untuk detail pengguna -->
    <div class="col-md-9">
      <div class="card">
        <div class="card-body" id="userDetail">
          <h5 class="card-title">Pilih pengguna untuk melihat detail</h5>
          <p class="card-text">Klik salah satu pengguna dari daftar kiri untuk melihat informasi lebih lanjut.</p>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Link ke Bootstrap 5 JS dan Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

<script>
  // Simulasi data pengguna
  const users = <?php echo json_encode($penggajian); ?>;
  console.log((users[6]))
  // Fungsi untuk menampilkan detail pengguna di kolom kanan
  function showUserDetail(id) {
          const userDetail = `
      <h5 class="card-title">${users[id].nama}</h5>
      <p class="card-text"><strong>masuk:</strong> ${users[id].masuk} / ${users[id].hari_kerja} Hari</p>
      <p class="card-text"><strong>Telat:</strong> ${users[id].telat} Hari</p>
      <p class="card-text"><strong>sakit:</strong> ${users[id].sakit} Hari</p>
      <p class="card-text"><strong>Gaji:</strong> ${formatRupiah(users[id].gaji)} </p>
    `;
    document.getElementById('userDetail').innerHTML = userDetail; // Return the user data
}

function formatRupiah(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0 // Optional: Remove decimal points if not needed
    }).format(amount);
}
</script>

<!-- kalender -->
<!-- <div class="row">
  <div class="col">
    <div class="card">
      <div class="card-header">
        <h4>Detail Absen <?= $nama ?></h4>
        <div id="calendar" class="fc fc-media-screen fc-direction-ltr fc-theme-bootstrap my-3 mx-3">
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header text-center">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Modal title</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
          <img src="" class="img-thumbnail" width="200" alt="">
          <div class="waktu"></div>
        </div>
        <div class="modal-footer">
          <div class="container-fluid">
            <div class="row">
              <div class="col-md-4 ">
                <button type="button" class="btn btn-danger" disabled>Terlambat : <span class="terlambat"></span></button>
              </div>
              <div class="col-md-4 ms-auto ">
                <button type="button" class="btn btn-warning" disabled>sakit : <span class="sakit"></span></button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div> -->


  <!-- akhir kalender -->
  <script src="https://code.jquery.com/jquery-3.6.4.js" integrity="sha256-a9jBBRygX1Bh5lt8GZjXDzyOB+bWve9EiO7tROUtj/E=" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
  <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.7/index.global.min.js'></script>
  <script src="js/bos.js"></script>
  <script src="js/moment.js"></script>
  <script>
    $('.bulan-gaji').text(moment().locale('id').format('MMMM YYYY'));
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
  </script>

<script>
  document.addEventListener('click', function(event) {
  // Periksa apakah klik tersebut bukan di dalam card atau tombol
  const isClickInsideCard = event.target.closest('.card-body') || event.target.closest('.btn');
  
  // Jika klik di luar card dan button, maka tutup semua collapse
  if (!isClickInsideCard) {
    const collapseElements = document.querySelectorAll('.collapse.show');
    
    // Hapus class 'show' dari semua elemen collapse yang terbuka
    collapseElements.forEach(function(collapse) {
      bootstrap.Collapse.getInstance(collapse).hide();
    });
  }
});
</script>
  <?= $this->endSection() ?>