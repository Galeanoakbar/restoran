<!DOCTYPE html>
<?php
include "connection/koneksi.php";
session_start();
ob_start();

$id = $_SESSION['id_user'];

if(isset($_SESSION['edit_order'])){
  unset($_SESSION['edit_order']);
}

if(isset ($_SESSION['username'])){
  
  $query = "select * from user natural join level where id_user = $id";
  mysqli_query($conn, $query);
  $sql = mysqli_query($conn, $query);

  while($r = mysqli_fetch_array($sql)){
    $nama_user = $r['nama_user'];

?>
<html lang="en">
<head>
  <title>Entri Transaksi</title>
  <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <style>
/* General styles */
body {
    font-family: 'Arial', sans-serif; /* Menetapkan font keluarga */
    background-color: #f4f4f9; /* Warna latar belakang body */
    margin: 0; /* Menghilangkan margin default */
    padding: 0; /* Menghilangkan padding default */
}

/* Sidebar styles */
.sidebar {
    height: 100vh; /* Menyesuaikan tinggi sidebar agar memenuhi viewport */
    width: 280px; /* Lebar sidebar */
    background: #212529; /* Warna latar sidebar */
    color: #fff; /* Warna teks di sidebar */
    position: fixed; /* Memperbaiki posisi sidebar pada viewport */
    padding: 20px; /* Padding dalam sidebar */
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2); /* Memberikan efek bayangan pada sidebar */
    transition: transform 0.3s ease; /* Efek animasi untuk pergerakan */
    transform: translateX(0); /* Posisi default sidebar */
}

.sidebar.closed {
    transform: translateX(-100%); /* Menyembunyikan sidebar keluar dari viewport */
}

/* Heading di sidebar */
.sidebar h3 {
    text-align: center; /* Menengahkan teks */
    margin-top: 80px; /* Memberikan ruang atas */
    margin-bottom: 40px; /* Memberikan ruang bawah */
    color: #17a2b8; /* Warna teks */
}

/* Link di sidebar */
.sidebar a {
    display: flex;
    align-items: center;
    padding: 12px 15px; /* Padding di dalam link */
    margin-bottom: 10px; /* Memberikan ruang bawah antar link */
    color: #adb5bd; /* Warna teks link */
    text-decoration: none; /* Menghapus garis bawah */
    border-radius: 5px; /* Membulatkan sudut */
    transition: background 0.3s ease, color 0.3s ease; /* Efek transisi untuk hover */
}

/* Ketika hover di link sidebar */
.sidebar a:hover {
    background: #17a2b8; /* Warna latar hover */
    color: #fff; /* Warna teks hover */
}

/* Ikon pada link */
.sidebar a i {
    margin-right: 10px; /* Memberikan ruang antara ikon dan teks */
}

/* Main container dan layout */
.container {
    margin-left: 20px; /* Margin kiri untuk container supaya tidak tertutup sidebar */
    padding: 20px; /* Padding di dalam container */
}

/* Konten utama */
.content {
    margin-left: 280px; /* Margin kiri agar konten sejajar dengan sidebar */
    padding: 20px; /* Padding dalam konten */
    transition: margin-left 0.3s ease; /* Efek transisi saat sidebar dibuka/tutup */
}

/* Ketika sidebar terbuka */
.content.shifted {
    margin-left: 100px; /* Menggeser konten agar terlihat di samping sidebar */
}

/* Tabel styling */
.table {
    width: 100%; /* Lebar tabel penuh */
    border-collapse: collapse; /* Menghapus space antar sel */
    margin-top: 20px; /* Memberikan ruang atas tabel */
    background-color: #f4f4f9; /* Warna latar tabel */
}

.table th, .table td {
    padding: 12px; /* Padding di dalam sel tabel */
    text-align: center; /* Menengahkan teks di sel */
    border-bottom: 1px solid #ddd; /* Garis bawah antar sel */
}

.table th {
    background-color: #f4f4f9; /* Warna latar header tabel */
    font-weight: bold; /* Huruf tebal pada header tabel */
}

.table td {
    background-color: #fff; /* Warna latar sel tabel */
}

.table tr:hover {
    background-color: #f1f1f1; /* Warna latar sel saat hover */
}

/* Widget title */
.widget-title {
    background-color: #1a73e8; /* Warna latar widget */
    color: white; /* Warna teks widget */
    padding: 10px; /* Padding di dalam widget */
    font-size: 18px; /* Ukuran teks */
    border-radius: 8px 8px 0 0; /* Membulatkan sudut atas */
}

/* Tombol styling */
.btn {
    padding: 8px 16px; /* Padding di dalam tombol */
    font-size: 14px; /* Ukuran teks di tombol */
    border-radius: 5px; /* Membulatkan sudut tombol */
    cursor: pointer; /* Memberikan efek pointer saat hover */
    transition: background-color 0.3s ease; /* Efek transisi untuk hover */
    border: none; /* Menghilangkan border tombol */
}

/* Tombol sukses */
.btn-success {
    background-color: #4CAF50; /* Warna latar tombol sukses */
    color: white; /* Warna teks tombol sukses */
}

/* Hover pada tombol sukses */
.btn-success:hover {
    background-color: #45a049; /* Warna latar hover tombol sukses */
}

/* Tombol gagal */
.btn-danger {
    background-color: #f44336; /* Warna latar tombol gagal */
    color: white; /* Warna teks tombol gagal */
}

/* Hover pada tombol gagal */
.btn-danger:hover {
    background-color: #e53935; /* Warna latar hover tombol gagal */
}

/* Tombol untuk toggle sidebar */
.toggle-btn {
    position: fixed; /* Memperbaiki posisi tombol */
    top: 20px; /* Posisi atas tombol */
    left: 20px; /* Posisi kiri tombol */
    z-index: 1000; /* Menjadikan tombol paling atas */
    background-color: #17a2b8; /* Warna latar tombol */
    color: #fff; /* Warna teks tombol */
    border: none; /* Menghilangkan border tombol */
    border-radius: 5px; /* Membulatkan sudut tombol */
    padding: 10px 15px; /* Padding di dalam tombol */
    cursor: pointer; /* Memberikan efek pointer saat hover */
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2); /* Memberikan efek bayangan pada tombol */
}

/* Hover pada tombol toggle */
.toggle-btn:hover {
    transform: scale(1.05);
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
}

/* Media query untuk perangkat kecil */
@media (max-width: 768px) {
    .sidebar {
        width: 200px; /* Lebar sidebar di perangkat kecil */
    }

    .container {
        margin-left: 0; /* Margin kiri konten saat sidebar ditutup di perangkat kecil */
    }

    .content {
        margin-left: 0; /* Margin kiri konten saat sidebar ditutup di perangkat kecil */
    }

    .table {
        margin-left: 0; /* Margin tabel supaya tetap sejajar */
    }
}


  </style>
</head>
<body>
    <!-- Toggle Button -->
  <button class="toggle-btn" onclick="toggleSidebar()">☰ Menu</button>

  <!-- Sidebar -->
  <div class="sidebar" id="sidebar">
      <h3>Welcome, <?php echo htmlspecialchars($r['nama_user']); ?></h3>
      <ul>
          <?php
          if ($r['id_level'] == 1) { // Administrator = 1
          ?>
              <a href="beranda.php"><i class="fas fa-home"></i> Beranda</a>
              <a href="entri_referensi.php"><i class="fas fa-utensils"></i> Entri Referensi</a>
              <a href="entri_order.php"><i class="fas fa-shopping-cart"></i> Entri Order</a>
              <a href="entri_transaksi.php"><i class="fas fa-money-bill"></i> Entri Transaksi</a>
              <a href="generate_laporan.php"><i class="fas fa-print"></i> Generate Laporan</a>
          <?php
          } elseif ($r['id_level'] == 2) { // Waiter = 2
          ?>
              <a href="beranda.php"><i class="fas fa-home"></i> Beranda</a>
              <a href="entri_order.php"><i class="fas fa-shopping-cart"></i> Entri Order</a>
              <a href="generate_laporan.php"><i class="fas fa-print"></i> Generate Laporan</a>
          <?php
          } elseif ($r['id_level'] == 3) { // Kasir = 3
          ?>
              <a href="entri_transaksi.php"><i class="fas fa-money-bill"></i> Entri Transaksi</a>
              <a href="generate_laporan.php"><i class="fas fa-print"></i> Generate Laporan</a>
          <?php
          } elseif ($r['id_level'] == 4) { // Owner = 4
          ?>
              <a href="generate_laporan.php"><i class="fas fa-print"></i> Generate Laporan</a>
          <?php
          } elseif ($r['id_level'] == 5) { // Pelanggan = 5
          ?>
              <a href="entri_order.php"><i class="fas fa-shopping-cart"></i> Entri Order</a>
          <?php
          }
          ?>
          <a href="logout.php" class="btn btn-danger w-100 mt-3"><i class="fas fa-sign-out-alt"></i> Logout</a>
      </ul>
  </div>
<br>
<br>
  <!-- Main content -->
<div class="content" id="content">
  <div class="container">
    <div class="row-fluid">
      <?php
        if($r['id_level'] == 1 || $r['id_level'] == 3){
      ?>
      <div class="span7">
        <div class="widget-box">
          <div class="widget-title"><span class="icon"><i class="icon-th-large"></i></span>
            <h5>Belum Bayar</h5>
          </div>
            <table class="table">
              <thead>
                <tr>
                  <th>No. Meja</th>
                  <th>Pemesan</th>
                  <th>Total Harga</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php
                  $query_order = "select * from order_pesanan left join user on order_pesanan.id_pengunjung = user.id_user where status_order = 'belum bayar'";
                  $sql_order = mysqli_query($conn, $query_order);
                  while($r_order = mysqli_fetch_array($sql_order)){
                ?>
                <tr>
                  <td><?php echo $r_order['no_meja']; ?>.</td>
                  <td><?php echo $r_order['nama_user'];?></td>
                  <td>Rp. <?php echo $r_order['total_harga'];?>,-</td>
                  <td>
                    <form action="" method="post">
                      <button type="submit" value="<?php echo $r_order['id_order'];?>" name="edit_order" class="btn btn-success btn-mini"><i class='icon-pencil'></i>&nbsp;Transaksi</button>
                      <button type="submit" value="<?php echo $r_order['id_order'];?>" name="hapus_order" class="btn btn-danger btn-mini"><i class='icon icon-trash'></i>&nbsp;Hapus</button>
                    </form>
                  </td>
                </tr>
                <?php
                  }
                  if(isset($_REQUEST['edit_order'])){
                    $id_order = $_REQUEST['edit_order'];
                    $_SESSION['edit_order'] = $id_order;
                    header('location: transaksi.php');
                  }

                  if(isset($_REQUEST['hapus_order'])){
                    $id_order = $_REQUEST['hapus_order'];
                    $query_hapus_order = "delete from order_pesanan where id_order = $id_order";
                    $query_hapus_pesan_order = "delete from tb_pesan where id_order = $id_order";
                    $sql_hapus_order = mysqli_query($conn, $query_hapus_order);
                    $sql_hapus_pesan_order = mysqli_query($conn, $query_hapus_pesan_order);
                    if($sql_hapus_order){
                      header('location: entri_transaksi.php');
                    }
                  }
                ?>
              </tbody>
            </table>
        </div>
      </div>
    </div>  
  </div>    

  <div class="container">
    <div class="row-fluid"></div>
      <div class="span9">
        <div class="widget-box">
          <div class="widget-title"><span class="icon"><i class="icon-th-large"></i></span>
            <h5>Transaksi Terdahulu</h5>
          </div>
            <table class="table">
              <thead>
                <tr>
                  <th>No.</th>
                  <th>Waktu Pesan</th>
                  <th>Nama Pemesan</th>
                  <th>No Meja</th>
                  <th>Total Harga</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php
                  $nomor = 1;
                  $query_sudah_order = "select * from order_pesanan left join user on order_pesanan.id_pengunjung = user.id_user where status_order = 'sudah bayar' order by id_order desc";
                  $sql_sudah_order = mysqli_query($conn, $query_sudah_order);
                  while($r_sudah_order = mysqli_fetch_array($sql_sudah_order)){
                ?>
                <tr>
                  <td><?php echo $nomor++; ?>.</td>
                  <td><?php echo $r_sudah_order['waktu_pesan'];?></td>
                  <td><?php echo $r_sudah_order['nama_user'];?></td>
                  <td><?php echo $r_sudah_order['no_meja'];?></td>
                  <td>Rp. <?php echo $r_sudah_order['total_harga'];?>,-</td>
                  <td>
                    <form action="" method="post">
                      <button type="submit" value="<?php echo $r_sudah_order['id_order'];?>" name="hapus_transaksi" class="btn btn-danger btn-mini">
                        <i class='icon icon-trash'></i>&nbsp;Hapus
                      </button>
                      <a target='_blank' href="cetak_transaksi.php?konten=<?php echo $r_sudah_order['id_order'];?>" class="btn btn-success btn-mini">
                        <i class='icon icon-print'></i>&nbsp;Cetak
                      </a>
                    </form>
                  </td>
                </tr>
                <?php
                  }
                  if(isset($_REQUEST['hapus_transaksi'])){
                    $id_order = $_REQUEST['hapus_transaksi'];
                    $query_hapus_transaksi = "delete from order_pesanan where id_order = $id_order";
                    $query_hapus_pesan = "delete from tb_pesan where id_order = $id_order";
                    $sql_hapus_transaksi = mysqli_query($conn, $query_hapus_transaksi);
                    $sql_hapus_pesan = mysqli_query($conn, $query_hapus_pesan);
                    if($sql_hapus_transaksi){
                      header('location: entri_transaksi.php');
                    }
                  }
                ?>
              </tbody>
            </table>
        </div>
      </div>
  </div>
      <?php
        }
      ?>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('content');
            sidebar.classList.toggle('closed');
            content.classList.toggle('shifted');
        }
    </script>

</body>
</html>
<?php
  }
} else {
  header('location: logout.php');
}
ob_flush();
?>
