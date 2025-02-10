<?php
include "connection/koneksi.php";
session_start();
ob_start();

// Pastikan user sudah login
if (!isset($_SESSION['username'])) {
    header('location: logout.php');
    exit();
}

$id = $_SESSION['id_user'];

// Ambil data user dan level
$query = "SELECT * FROM user NATURAL JOIN level WHERE id_user = $id";
$sql = mysqli_query($conn, $query);
if (!$sql) {
    die("Query error: " . mysqli_error($conn));
}
$user = mysqli_fetch_array($sql);
$nama_user = $user['nama_user'];
$user_level = $user['id_level'];

// Inisialisasi variabel pendapatan dan data chart
$uang = 0;
$labels = [];
$data_revenue = [];

// Ambil data menu dari tabel masakan
$query_lihat_menu = "SELECT * FROM masakan";
$sql_lihat_menu = mysqli_query($conn, $query_lihat_menu);
if (!$sql_lihat_menu) {
    die("Query error: " . mysqli_error($conn));
}

// Hitung pendapatan per menu
while ($r_lihat_menu = mysqli_fetch_array($sql_lihat_menu)) {
    $id_masakan = $r_lihat_menu['id_masakan'];
    
    $query_jumlah = "SELECT SUM(jumlah_terjual) AS jumlah_terjual 
                     FROM stok_menu 
                     LEFT JOIN tb_pesan ON stok_menu.id_pesan = tb_pesan.id_pesan 
                     WHERE id_masakan = $id_masakan AND status_cetak = 'belum cetak'";
    $sql_jumlah = mysqli_query($conn, $query_jumlah);
    if (!$sql_jumlah) {
        die("Query error: " . mysqli_error($conn));
    }
    $result_jumlah = mysqli_fetch_array($sql_jumlah);
    $jml = $result_jumlah['jumlah_terjual'] ?? 0;
    $total_pendapatan = $jml * $r_lihat_menu['harga'];
    $uang += $total_pendapatan;
    
    $labels[] = $r_lihat_menu['nama_masakan'];
    $data_revenue[] = $total_pendapatan;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan - Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
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
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            transition: transform 0.3s ease;
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
        .sidebar ul {
            list-style: none;
            padding: 0;
        }
        .sidebar ul li {
            margin-bottom: 10px;
        }
        .sidebar ul li a {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: #adb5bd;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s ease, color 0.3s ease;
        }
        .sidebar ul li a:hover {
            background: #17a2b8;
            color: #fff;
        }
        .sidebar ul li a i {
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
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <!-- Tombol Toggle Sidebar -->
    <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
    
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <h3>Welcome, <?php echo htmlspecialchars($nama_user); ?></h3>
        <ul>
            <?php
            if ($user_level == 1) { // Administrator
                echo '<li><a href="beranda.php"><i class="fas fa-home"></i> Beranda</a></li>';
                echo '<li><a href="entri_referensi.php"><i class="fas fa-utensils"></i> Entri Referensi</a></li>';
                echo '<li><a href="entri_order.php"><i class="fas fa-shopping-cart"></i> Entri Order</a></li>';
                echo '<li><a href="entri_transaksi.php"><i class="fas fa-money-bill"></i> Entri Transaksi</a></li>';
                echo '<li><a href="generate_laporan.php"><i class="fas fa-print"></i> Generate Laporan</a></li>';
            } elseif ($user_level == 2) { // Waiter
                echo '<li><a href="entri_order.php"><i class="fas fa-shopping-cart"></i> Entri Order</a></li>';
                echo '<li><a href="generate_laporan.php"><i class="fas fa-print"></i> Generate Laporan</a></li>';
            } elseif ($user_level == 3) { // Kasir
                echo '<li><a href="entri_transaksi.php"><i class="fas fa-money-bill"></i> Entri Transaksi</a></li>';
                echo '<li><a href="generate_laporan.php"><i class="fas fa-print"></i> Generate Laporan</a></li>';
            } elseif ($user_level == 4) { // Owner
                echo '<li><a href="generate_laporan.php"><i class="fas fa-print"></i> Generate Laporan</a></li>';
            } 
            ?>
            <li class="mt-3">
                <a href="logout.php" class="btn btn-danger w-100"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </li>
        </ul>
    </div>
    
    <!-- Konten Utama -->
    <div class="content" id="content">
        <div class="container-fluid">
            <h1 class="mt-4 mb-3">Laporan Penjualan</h1>
            <div class="mb-4">
                <h4>Total Uang Masuk: Rp. <?php echo number_format($uang, 0, ',', '.'); ?> ,-</h4>
                <!-- Misalnya, tambahkan tombol download laporan jika perlu -->
<a href="download_laporan.php" class="btn btn-success">
        <i class="fas fa-download"></i> Download Laporan
    </a>            </div>
            
            <!-- Tab Navigasi untuk Grafik dan Tabel -->
            <ul class="nav nav-tabs" id="reportTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="chart-tab" data-bs-toggle="tab" data-bs-target="#chart" type="button" role="tab" aria-controls="chart" aria-selected="true">
                        Grafik Pendapatan
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="table-tab" data-bs-toggle="tab" data-bs-target="#table" type="button" role="tab" aria-controls="table" aria-selected="false">
                        Tabel Penjualan
                    </button>
                </li>
            </ul>
            <div class="tab-content" id="reportTabContent">
                <!-- Tab Grafik -->
                <div class="tab-pane fade show active p-4" id="chart" role="tabpanel" aria-labelledby="chart-tab">
                    <canvas id="salesChart" style="max-height: 400px;"></canvas>
                </div>
                <!-- Tab Tabel -->
                <div class="tab-pane fade p-4" id="table" role="tabpanel" aria-labelledby="table-tab">
                    <div class="table-responsive">
                        <table id="salesTable" class="table table-striped table-bordered">
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
                                // Jalankan ulang query menu untuk tabel
                                $query_lihat_menu = "SELECT * FROM masakan";
                                $sql_lihat_menu = mysqli_query($conn, $query_lihat_menu);
                                if (!$sql_lihat_menu) {
                                    die("Query error: " . mysqli_error($conn));
                                }
                                $no = 1;
                                while ($r_lihat_menu = mysqli_fetch_array($sql_lihat_menu)) {
                                    $id_masakan = $r_lihat_menu['id_masakan'];
                                    $query_jumlah = "SELECT SUM(jumlah_terjual) AS jumlah_terjual 
                                                     FROM stok_menu 
                                                     LEFT JOIN tb_pesan ON stok_menu.id_pesan = tb_pesan.id_pesan 
                                                     WHERE id_masakan = $id_masakan AND status_cetak = 'belum cetak'";
                                    $sql_jumlah = mysqli_query($conn, $query_jumlah);
                                    if (!$sql_jumlah) {
                                        die("Query error: " . mysqli_error($conn));
                                    }
                                    $result_jumlah = mysqli_fetch_array($sql_jumlah);
                                    $jml = $result_jumlah['jumlah_terjual'] ?? 0;
                                    $total_pendapatan = $jml * $r_lihat_menu['harga'];
                                ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><?php echo htmlspecialchars($r_lihat_menu['nama_masakan']); ?></td>
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
            </div>
        </div>
    </div>
    
    <!-- jQuery, Bootstrap JS, DataTables, dan Chart.js -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Toggle sidebar dengan animasi
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('content');
            const toggleBtn = document.querySelector('.toggle-btn');
            sidebar.classList.toggle('closed');
            content.classList.toggle('shifted');
            toggleBtn.innerHTML = sidebar.classList.contains('closed') ? '☰' : '✖';
        }
        
        // Inisialisasi DataTables
        $(document).ready(function(){
            $('#salesTable').DataTable();
        });
        
        // Inisialisasi Chart.js untuk grafik pendapatan
        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($labels); ?>,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: <?php echo json_encode($data_revenue); ?>,
                    backgroundColor: 'rgba(23, 162, 184, 0.5)',
                    borderColor: 'rgba(23, 162, 184, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp. ' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
