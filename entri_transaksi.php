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
    font-family: 'Arial', sans-serif;
    background-color: #f4f4f9;
    margin: 0;
    padding: 0;
}

/* Sidebar styles */
.sidebar {
    height: 100vh;
    width: 280px;
    background: #212529;
    color: #fff;
    position: fixed;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    transition: transform 0.3s ease;
    transform: translateX(0);
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

/* Main container dan layout */
.container {
    margin-left: 20px;
    padding: 20px;
}

/* Konten utama */
.content {
    margin-left: 280px;
    padding: 20px;
    transition: margin-left 0.3s ease;
}

.content.shifted {
    margin-left: 100px;
}

/* Tabel styling */
.table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    background-color: #f4f4f9;
}

.table th, .table td {
    padding: 12px;
    text-align: center;
    border-bottom: 1px solid #ddd;
}

.table th {
    background-color: #f4f4f9;
    font-weight: bold;
}

.table td {
    background-color: #fff;
}

.table tr:hover {
    background-color: #f1f1f1;
}

/* Widget title */
.widget-title {
    background-color: #1a73e8;
    color: white;
    padding: 10px;
    font-size: 18px;
    border-radius: 8px 8px 0 0;
}

/* Tombol styling */
.btn {
    padding: 8px 16px;
    font-size: 14px;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    border: none;
}

.btn-success {
    background-color: #4CAF50;
    color: white;
}

.btn-success:hover {
    background-color: #45a049;
}

.btn-danger {
    background-color: #f44336;
    color: white;
}

.btn-danger:hover {
    background-color: #e53935;
}

/* Tombol untuk toggle sidebar */
.toggle-btn {
    position: fixed;
    top: 20px;
    left: 20px;
    z-index: 1000;
    background-color: #17a2b8;
    color: #fff;
    border: none;
    border-radius: 5px;
    padding: 10px 15px;
    cursor: pointer;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

.toggle-btn:hover {
    transform: scale(1.05);
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
}

/* Media query untuk perangkat kecil */
@media (max-width: 768px) {
    .sidebar {
        width: 200px;
    }

    .container {
        margin-left: 0;
    }

    .content {
        margin-left: 0;
    }

    .table {
        margin-left: 0;
    }
}
  </style>
</head>
<body>
    <!-- Toggle Button -->
  <button class="toggle-btn" onclick="toggleSidebar()">☰ </button>

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
                    
                    // Ambil data pesanan dari tb_pesan untuk mengembalikan stok
                    $query_get_items = "select * from tb_pesan where id_order = $id_order";
                    $sql_get_items = mysqli_query($conn, $query_get_items);
                    while($item = mysqli_fetch_array($sql_get_items)){
                        $id_masakan = $item['id_masakan'];
                        $jumlah = $item['jumlah'];
                        // Kembalikan stok: tambahkan kembali jumlah yang telah dipesan
                        $query_update_stok = "update masakan set stok = stok + $jumlah where id_masakan = $id_masakan";
                        mysqli_query($conn, $query_update_stok);
                    }
                    
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
        const toggleBtn = document.querySelector('.toggle-btn');
        
        sidebar.classList.toggle('closed');
        content.classList.toggle('shifted');
        
        toggleBtn.innerHTML = sidebar.classList.contains('closed') ? '☰' : '✖';
    }

    // Inisialisasi tooltips Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

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
