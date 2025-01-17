<!DOCTYPE html>
<?php
include "connection/koneksi.php";
session_start();
ob_start();

$id = $_SESSION['id_user'];

if (isset($_SESSION['edit_order'])) {
    unset($_SESSION['edit_order']);
}

if (isset($_SESSION['username'])) {
    $query = "SELECT * FROM user NATURAL JOIN level WHERE id_user = $id";
    $sql = mysqli_query($conn, $query);

    while ($r = mysqli_fetch_array($sql)) {
        $nama_user = $r['nama_user'];
        $uang = 0;
?>

<html lang="en">
<head>
    <title>Entri Transaksi</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f1f5f9;
            overflow-x: hidden;
        }
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
            margin-top: 80px; /* Tambahkan margin atas untuk menghindari tombol menu */
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
        .content {
            margin-left: 300px;
            padding: 20px;
            transition: margin-left 0.3s ease;
        }
        .content.shifted {
            margin-left: 20px;
        }
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
        
    </style>
</head>
<body>
<button class="toggle-btn" onclick="toggleSidebar()">☰ Menu</button>
<div class="sidebar" id="sidebar">
    <h3>Welcome, <?php echo htmlspecialchars($nama_user); ?></h3>
    <ul>
        <?php
        if ($r['id_level'] == 1) { 
        ?>
            <a href="beranda.php"><i class="fas fa-home"></i> Beranda</a></li>
            <a href="entri_referensi.php"><i class="fas fa-utensils"></i> Entri Referensi</a>
            <a href="entri_order.php"><i class="fas fa-shopping-cart"></i> Entri Order</a>
            <a href="entri_transaksi.php"><i class="fas fa-money-bill"></i> Entri Transaksi</a>
            <a href="generate_laporan.php"><i class="fas fa-print"></i> Generate Laporan</a>
        <?php
        } elseif ($r['id_level'] == 2) { // Level 2
        ?>
            <a href="beranda.php"><i class="fas fa-home"></i> Beranda</a>
            <a href="entri_order.php"><i class="fas fa-shopping-cart"></i> Entri Order</a>
            <a href="generate_laporan.php"><i class="fas fa-print"></i> Generate Laporan</a>
        <?php
        } elseif ($r['id_level'] == 3) { // Level 3
        ?>
            <a href="beranda.php"><i class="fas fa-home"></i> Beranda</a>
            <a href="entri_transaksi.php"><i class="fas fa-money-bill"></i> Entri Transaksi</a>
            <a href="generate_laporan.php"><i class="fas fa-print"></i> Generate Laporan</a>
        <?php
        } elseif ($r['id_level'] == 4) { // Level 4
        ?>
            <a href="beranda.php"><i class="fas fa-home"></i> Beranda</a>
            <a href="generate_laporan.php"><i class="fas fa-print"></i> Generate Laporan</a>
        <?php
        } elseif ($r['id_level'] == 5) { // Level 5
        ?>
            <a href="entri_order.php"><i class="fas fa-shopping-cart"></i> Entri Order</a>
        <?php
        }
        ?>
        <a href="logout.php" class="btn btn-danger w-100 mt-3"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </ul>
</div>

    <br><br><br>
    <div class="content" id="content">
        <?php if ($r['id_level'] >= 1 && $r['id_level'] <= 4): ?>
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="m-0">Laporan Penjualan</h5>
                </div>
                <div class="card-body">
                    <table class="table table-hover table-bordered">
                        <thead class="table-primary">
                            <tr>
                                <th>No.</th>
                                <th>Nama Menu</th>
                                <th>Sisa Stok</th>
                                <th>Jumlah Terjual</th>
                                <th>Harga</th>
                                <th>Total Pendapatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $query_lihat_menu = "SELECT * FROM masakan";
                            $sql_lihat_menu = mysqli_query($conn, $query_lihat_menu);

                            while ($r_lihat_menu = mysqli_fetch_array($sql_lihat_menu)) {
                                $id_masakan = $r_lihat_menu['id_masakan'];
                                $query_jumlah = "SELECT SUM(jumlah_terjual) AS jumlah_terjual 
                                                 FROM stok_menu 
                                                 LEFT JOIN tb_pesan ON stok_menu.id_pesan = tb_pesan.id_pesan 
                                                 WHERE id_masakan = $id_masakan AND status_cetak = 'belum cetak'";
                                $sql_jumlah = mysqli_query($conn, $query_jumlah);
                                $result_jumlah = mysqli_fetch_array($sql_jumlah);

                                $jml = $result_jumlah['jumlah_terjual'] ?? 0;
                                $total_pendapatan = $jml * $r_lihat_menu['harga'];
                                $uang += $total_pendapatan;
                            ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo $r_lihat_menu['nama_masakan']; ?></td>
                                <td><?php echo $r_lihat_menu['stok']; ?></td>
                                <td><?php echo $jml; ?></td>
                                <td>Rp. <?php echo number_format($r_lihat_menu['harga'], 0, ',', '.'); ?></td>
                                <td>Rp. <?php echo number_format($total_pendapatan, 0, ',', '.'); ?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="mt-4">
                <h4>Total Uang Masuk: Rp. <?php echo number_format($uang, 0, ',', '.'); ?> ,-</h4>
            </div>
        <?php endif; ?>
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
