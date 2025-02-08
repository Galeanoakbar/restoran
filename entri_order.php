<?php
include "connection/koneksi.php";
session_start();
ob_start();

// Jika sesi tidak ada, redirect ke logout
if (!isset($_SESSION['username'])) {
    header('location: logout.php');
    exit();
}

$id = $_SESSION['id_user'];

// Tampilkan pesan edit_menu jika ada (gunakan print_r bila berupa array)
if (isset($_SESSION['edit_menu'])) {
    echo '<pre>';
    print_r($_SESSION['edit_menu']);
    echo '</pre>';
    unset($_SESSION['edit_menu']);
}

// Ambil data user (asumsi hanya satu record)
$query = "SELECT * FROM user NATURAL JOIN level WHERE id_user = $id";
$sql = mysqli_query($conn, $query);
$r = mysqli_fetch_array($sql);
$nama_user = $r['nama_user'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Entri Order - Modern & Interactive</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap 5 CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome untuk ikon -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Custom CSS -->
  <style>
    body {
      background-color: #f4f6f9;
      font-family: 'Open Sans', sans-serif;
    }
    /* Style untuk tombol toggle sidebar */
        .toggle-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1100;
            background-color: #17a2b8;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 10px 15px;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
    /* Sidebar styling */
    .sidebar {
      height: 100vh;
      width: 280px;
      background: #212529;
      color: #fff;
      position: fixed;
      left: 0;
      top: 0;
      padding: 20px;
      box-shadow: 2px 0 10px rgba(0,0,0,0.2);
      transition: transform 0.3s ease;
      z-index: 1050;
    }
    .sidebar.closed {
      transform: translateX(-100%);
    }
    .sidebar h3 {
      text-align: center;
      margin-top: 80px;
      margin-bottom: 40px;
      color: #17a2b8;
    }
    .sidebar a {
      display: flex;
      align-items: center;
      padding: 12px 15px;
      margin-bottom: 10px;
      color: #adb5bd;
      text-decoration: none;
      border-radius: 5px;
      transition: background 0.3s ease, color 0.3s ease;
    }
    .sidebar a:hover {
      background: #17a2b8;
      color: #fff;
    }
    .sidebar a i {
      margin-right: 10px;
    }
    /* Style tambahan untuk konten halaman */
    .content {
      margin-left: 300px;
      padding: 20px;
      transition: margin-left 0.3s ease;
    }
    .sidebar.closed ~ .content {
      margin-left: 20px;
    }
    /* Pengaturan Card Menu agar seragam dan interaktif */
    .menu-card {
      height: 350px; /* tinggi seragam untuk semua kartu */
      display: flex;
      flex-direction: column;
      transition: transform 0.3s, box-shadow 0.3s;
    }
    .menu-card:hover {
      transform: scale(1.05);
      box-shadow: 0 8px 16px rgba(0,0,0,0.2);
    }
    .menu-card img {
      height: 200px; /* tinggi gambar tetap */
      object-fit: cover;
    }
    .menu-card .card-body {
      flex: 1;
      display: flex;
      flex-direction: column;
    }
    .menu-card .card-body h5 {
      font-size: 1.25rem;
      margin-bottom: 0.5rem;
    }
    .menu-card .card-body p {
      margin-bottom: 0.5rem;
    }
  </style>
</head>
<body>
    <!-- Toggle Button -->
    <button class="toggle-btn" onclick="toggleSidebar()">☰</button>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <h3>Welcome, <?= htmlspecialchars($r['nama_user']); ?></h3>
        <ul class="list-unstyled">
            <?php if ($r['id_level'] == 1): // Administrator ?>
                <li><a href="beranda.php"><i class="fas fa-home"></i> Beranda</a></li>
                <li><a href="entri_referensi.php"><i class="fas fa-utensils"></i> Entri Referensi</a></li>
                <li><a href="entri_order.php"><i class="fas fa-shopping-cart"></i> Entri Order</a></li>
                <li><a href="entri_transaksi.php"><i class="fas fa-money-bill"></i> Entri Transaksi</a></li>
                <li><a href="generate_laporan.php"><i class="fas fa-print"></i> Generate Laporan</a></li>
            <?php elseif ($r['id_level'] == 2): // Waiter ?>
                <li><a href="beranda.php"><i class="fas fa-home"></i> Beranda</a></li>
                <li><a href="entri_order.php"><i class="fas fa-shopping-cart"></i> Entri Order</a></li>
                <li><a href="generate_laporan.php"><i class="fas fa-print"></i> Generate Laporan</a></li>
            <?php elseif ($r['id_level'] == 3): // Kasir ?>
                <li><a href="entri_transaksi.php"><i class="fas fa-money-bill"></i> Entri Transaksi</a></li>
                <li><a href="generate_laporan.php"><i class="fas fa-print"></i> Generate Laporan</a></li>
            <?php elseif ($r['id_level'] == 4): // Owner ?>
                <li><a href="generate_laporan.php"><i class="fas fa-print"></i> Generate Laporan</a></li>
            <?php endif; ?>
            <li class="mt-3">
                <a href="logout.php" class="btn btn-danger w-100">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
    </div>

    <!-- Konten Halaman -->
    <div class="content">
      <div class="container my-4">
        <!-- User Greeting -->
        <div class="mb-4">
          <h2>Selamat Datang, <?php echo htmlspecialchars($nama_user); ?></h2>
        </div>

        <?php
        // Cek apakah user sudah memiliki order aktif
        $order = [];
        $query_lihat_order = "SELECT * FROM order_pesanan";
        $sql_lihat_order = mysqli_query($conn, $query_lihat_order);
        while ($r_dt_order = mysqli_fetch_array($sql_lihat_order)) {
            if ($r_dt_order['status_order'] != 'sudah bayar') {
                $order[] = $r_dt_order['id_pengunjung'];
            }
        }
        if (in_array($id, $order)) {
        ?>
          <!-- Tampilan jika order sudah ada -->
          <div class="alert alert-info" role="alert">
            <h4 class="alert-heading">Informasi Pesanan</h4>
            Terimakasih, Anda telah melakukan pemesanan. Silahkan tunggu pesanan tiba di meja saudara.
            Setelah selesai, lakukan pembayaran di kasir.
          </div>
          <!-- Detail Order -->
          <div class="card mb-4">
            <div class="card-header">
              Menu yang dipesan
            </div>
            <div class="card-body">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>No.</th>
                    <th>Menu</th>
                    <th class="text-center">Jumlah</th>
                    <th class="text-end">Harga</th>
                    <th class="text-end">Total</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $no_order_fiks = 1;
                  $query_order_fiks = "SELECT * FROM tb_pesan NATURAL JOIN masakan WHERE id_user = $id AND status_pesan != 'sudah'";
                  $sql_order_fiks = mysqli_query($conn, $query_order_fiks);
                  while ($r_order_fiks = mysqli_fetch_array($sql_order_fiks)) {
                  ?>
                  <tr>
                    <td><?php echo $no_order_fiks++; ?></td>
                    <td><?php echo $r_order_fiks['nama_masakan']; ?></td>
                    <td class="text-center"><?php echo $r_order_fiks['jumlah']; ?></td>
                    <td class="text-end">Rp. <?php echo number_format($r_order_fiks['harga'], 0, ',', '.'); ?></td>
                    <td class="text-end">Rp. <?php echo number_format($r_order_fiks['harga'] * $r_order_fiks['jumlah'], 0, ',', '.'); ?></td>
                  </tr>
                  <?php
                  }
                  $query_harga = "SELECT * FROM order_pesanan WHERE id_pengunjung = $id AND status_order = 'belum bayar'";
                  $sql_harga = mysqli_query($conn, $query_harga);
                  $result_harga = mysqli_fetch_array($sql_harga);
                  ?>
                  <tr>
                    <td colspan="4" class="text-end"><strong>Total</strong></td>
                    <td class="text-end"><strong>Rp. <?php echo number_format($result_harga['total_harga'], 0, ',', '.'); ?></strong></td>
                  </tr>
                  <tr>
                    <td colspan="4" class="text-end"><strong>No. Meja</strong></td>
                    <td class="text-center"><strong><?php echo $result_harga['no_meja']; ?></strong></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        <?php
        } else {
        ?>
          <!-- Tampilan menu dan keranjang pesanan -->
          <div class="row">
            <div class="col-md-8">
              <!-- Tampilan Menu dengan Kartu Modern yang Uniform & Interaktif -->
              <div class="row">
              <?php
              // Ambil daftar menu yang sudah dipesan agar tombol "Pesan" dinonaktifkan
              $pesan = [];
              $query_lihat_pesan = "SELECT * FROM tb_pesan WHERE id_user = $id AND status_pesan != 'sudah'";
              $sql_lihat_pesan = mysqli_query($conn, $query_lihat_pesan);
              while ($r_dt_pesan = mysqli_fetch_array($sql_lihat_pesan)) {
                  $pesan[] = $r_dt_pesan['id_masakan'];
              }
              $query_data_makanan = "SELECT * FROM masakan WHERE stok > 0 ORDER BY id_masakan DESC";
              $sql_data_makanan = mysqli_query($conn, $query_data_makanan);
              while ($r_dt_makanan = mysqli_fetch_array($sql_data_makanan)) {
              ?>
                <div class="col-md-4 mb-4">
                  <div class="card menu-card h-100 shadow-sm">
                    <img src="gambar/<?php echo $r_dt_makanan['gambar_masakan']; ?>" class="card-img-top" alt="<?php echo $r_dt_makanan['nama_masakan']; ?>">
                    <div class="card-body">
                      <h5 class="card-title"><?php echo $r_dt_makanan['nama_masakan']; ?></h5>
                      <p class="card-text mb-1">Harga: Rp. <?php echo number_format($r_dt_makanan['harga'], 0, ',', '.'); ?>,-</p>
                      <p class="card-text">Stok: <?php echo $r_dt_makanan['stok']; ?> Porsi</p>
                      <div class="mt-auto">
                        <form action="" method="post">
                          <?php if (in_array($r_dt_makanan['id_masakan'], $pesan)) { ?>
                            <button type="button" class="btn btn-danger w-100" disabled>
                              <i class="fas fa-shopping-cart"></i> Telah dipesan
                            </button>
                          <?php } else { ?>
                            <button type="submit" name="tambah_pesan" value="<?php echo $r_dt_makanan['id_masakan']; ?>" class="btn btn-success w-100">
                              <i class="fas fa-shopping-cart"></i> Pesan
                            </button>
                          <?php } ?>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
              <?php
              }
              // Proses penambahan pesanan
              if (isset($_REQUEST['tambah_pesan'])) {
                  $id_masakan = $_REQUEST['tambah_pesan'];
                  $query_tambah_pesan = "INSERT INTO tb_pesan VALUES('', '$id', '', '$id_masakan', '', '')";
                  $sql_tambah_pesan = mysqli_query($conn, $query_tambah_pesan);
                  $query_lihat_pesannya = "SELECT * FROM tb_pesan ORDER BY id_pesan DESC LIMIT 1";
                  $sql_lihat_pesannya = mysqli_query($conn, $query_lihat_pesannya);
                  $result_lihat_pesannya = mysqli_fetch_array($sql_lihat_pesannya);
                  $id_pesannya = $result_lihat_pesannya['id_pesan'];
                  $query_olah_stok = "INSERT INTO stok_menu VALUES('', '$id_pesannya', '', 'belum cetak')";
                  $sql_olah_stok = mysqli_query($conn, $query_olah_stok);
                  if ($sql_tambah_pesan) {
                      header('location: entri_order.php');
                  }
              }
              ?>
              </div>
            </div>
            <div class="col-md-4">
              <div class="card shadow-lg">
                  <!-- Header dengan latar berwarna dan judul yang lebih jelas -->
                  <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Keranjang Pemesanan</h5>
                  </div>
                  <div class="card-body">
                    <form action="" method="post">
                        <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                            <tr>
                                <th>Menu Pesanan</th>
                                <th class="text-center">Jumlah</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            // Ambil data pesanan yang belum selesai (status 'sudah' belum terpenuhi)
                            $query_draft_pesan = "SELECT * FROM tb_pesan NATURAL JOIN masakan WHERE id_user = $id AND status_pesan != 'sudah'";
                            $sql_draft_pesan = mysqli_query($conn, $query_draft_pesan);
                            while ($r_draft_pesan = mysqli_fetch_array($sql_draft_pesan)) {
                            ?>
                            <tr class="cart-item">
                                <td><?php echo htmlspecialchars($r_draft_pesan['nama_masakan']); ?></td>
                                <td class="text-center">
                                  <div class="input-group">
                                      <input type="number" name="jumlah<?php echo $r_draft_pesan['id_masakan']; ?>" id="jumlah<?php echo $r_draft_pesan['id_pesan']; ?>" 
                                          value="1" min="1" class="form-control text-center" onchange="hitungTotal()">
                                  </div>
                                  <!-- Simpan harga sebagai input tersembunyi untuk perhitungan -->
                                  <input type="hidden" id="harga<?php echo $r_draft_pesan['id_pesan']; ?>" value="<?php echo $r_draft_pesan['harga']; ?>">
                                </td>
                                <td class="text-center">
                                  <button type="submit" name="hapus_pesan" value="<?php echo $r_draft_pesan['id_pesan']; ?>" class="btn btn-sm btn-danger" title="Hapus Pesanan">
                                    <i class="fas fa-trash-alt"></i>
                                  </button>
                                </td>
                            </tr>
                            <?php } ?>
                            <!-- Input untuk nomor meja -->
                            <tr>
                                <td><strong>No. Meja</strong></td>
                                <td colspan="2">
                                  <input type="number" name="no_meja" class="form-control" placeholder="Masukkan no meja" required>
                                </td>
                            </tr>
                            <!-- Baris total harga -->
                            <tr>
                                <td colspan="2"><strong>Total Harga</strong></td>
                                <td class="text-end">
                                  <span class="badge bg-success">Rp. <span id="total_harga">0</span>,-</span>
                                  <input type="hidden" name="total_harga" id="tot" value="0">
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        </div>
                        <!-- Tombol proses pesanan yang lebih besar dan jelas -->
                        <div class="d-grid gap-2 mt-3">
                            <button type="submit" name="proses_pesan" class="btn btn-info btn-lg">
                              <i class="fas fa-share"></i> Proses Pesanan
                            </button>
                        </div>
                    </form>
                  </div>
              </div>
            </div>
          </div>
        <?php
        } // end if order aktif else

        // Proses hapus pesan
        if (isset($_POST['hapus_pesan'])) {
            $id_pesan = $_POST['hapus_pesan'];
            $query_hapus_pesan = "DELETE FROM tb_pesan WHERE id_pesan = $id_pesan";
            $sql_hapus_pesan = mysqli_query($conn, $query_hapus_pesan);
            if ($sql_hapus_pesan) {
                header('location: entri_order.php');
            }
        }

        // Proses order submission
        if (isset($_POST['proses_pesan'])) {
            $id_admin = '';
            $id_pengunjung = $id;
            $no_meja = $_POST['no_meja'];
            $total_harga = $_POST['total_harga'];
            $uang_bayar = '';
            $uang_kembali = '';
            $status_order = 'belum bayar';

            date_default_timezone_set('Asia/Jakarta');
            $time = date('YmdHis');
            $query_simpan_order = "INSERT INTO order_pesanan VALUES('', '$id_admin', '$id_pengunjung', $time, '$no_meja', '$total_harga', '$uang_bayar', '$uang_kembali', '$status_order')";
            $sql_simpan_order = mysqli_query($conn, $query_simpan_order);

            $query_tampil_order = "SELECT * FROM order_pesanan WHERE id_pengunjung = $id ORDER BY id_order DESC LIMIT 1";
            $sql_tampil_order = mysqli_query($conn, $query_tampil_order);
            $result_tampil_order = mysqli_fetch_array($sql_tampil_order);
            $id_ordernya = $result_tampil_order['id_order'];

            $query_ubah_jumlah = "SELECT * FROM tb_pesan LEFT JOIN masakan ON tb_pesan.id_masakan = masakan.id_masakan WHERE id_user = $id AND status_pesan != 'sudah'";
            $sql_ubah_jumlah = mysqli_query($conn, $query_ubah_jumlah);
            while ($r_ubah_jumlah = mysqli_fetch_array($sql_ubah_jumlah)) {
                $tahu = $r_ubah_jumlah['id_masakan'];
                $tempe = $_POST['jumlah' . $tahu];
                $id_pesan = $r_ubah_jumlah['id_pesan'];
                $query_stok = "SELECT * FROM masakan WHERE id_masakan = $tahu";
                $sql_stok = mysqli_query($conn, $query_stok);
                $result_stok = mysqli_fetch_array($sql_stok);
                $sisa_stok = $result_stok['stok'] - $tempe;
                $query_proses_ubah = "UPDATE tb_pesan SET jumlah = $tempe, id_order = $id_ordernya WHERE id_masakan = $tahu AND id_user = $id AND status_pesan != 'sudah'";
                $query_kurangi_stok = "UPDATE masakan SET stok = $sisa_stok WHERE id_masakan = $tahu";
                $query_kelola_stok = "UPDATE stok_menu SET jumlah_terjual = $tempe WHERE id_pesan = $id_pesan";
                mysqli_query($conn, $query_kelola_stok);
                mysqli_query($conn, $query_kurangi_stok);
                mysqli_query($conn, $query_proses_ubah);
            }
            if ($sql_simpan_order) {
                header('location: entri_order.php');
            }
        }
        ?>
      </div>
    </div>

  <!-- Bootstrap 5 JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <!-- jQuery untuk perhitungan sederhana (jika diperlukan) -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <!-- Script untuk interaktivitas sidebar dan perhitungan total harga -->
  <script>
        // Toggle Sidebar dengan mengganti ikon tombol
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('closed');
            const toggleBtn = document.querySelector('.toggle-btn');
            toggleBtn.innerHTML = sidebar.classList.contains('closed') ? '☰' : '✖';
        }
        
        // Inisialisasi tooltips Bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
          return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    // Fungsi untuk menghitung total harga keranjang secara dinamis
    function hitungTotal() {
      var total = 0;
      $("tr.cart-item").each(function(){
        var harga = parseFloat($(this).find("input[id^='harga']").val()) || 0;
        var jumlah = parseFloat($(this).find("input[type='number']").val()) || 0;
        total += harga * jumlah;
      });
      $("#total_harga").text(total.toLocaleString());
      $("#tot").val(total);
    }
    $("input[type='number']").on("input", hitungTotal);
  </script>
</body>
</html>
<?php
ob_flush();
?>
