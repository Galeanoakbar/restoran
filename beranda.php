<?php
include "connection/koneksi.php";
session_start();
ob_start();

// Pastikan user sudah login
if (!isset($_SESSION['username'])) {
    header('location: logout.php');
    exit;
}

// PROSES AKSI PENGGUNA (validasi, unvalidasi, hapus) melalui form submission
if (isset($_POST['hapus_user'])) {
    $id_user = $_POST['hapus_user'];
    $query_hapus_user = "DELETE FROM user WHERE id_user = $id_user";
    $sql_hapus_user = mysqli_query($conn, $query_hapus_user);
    if ($sql_hapus_user) {
        header('location: beranda.php');
        exit;
    }
}

if (isset($_POST['validasi'])) {
    $id_user = $_POST['validasi'];
    $query_validasi = "UPDATE user SET status = 'aktif' WHERE id_user = $id_user";
    $sql_validasi = mysqli_query($conn, $query_validasi);
    if ($sql_validasi) {
        header('location: beranda.php');
        exit;
    }
}

if (isset($_POST['unvalidasi'])) {
    $id_user = $_POST['unvalidasi'];
    $query_unvalidasi = "UPDATE user SET status = 'nonaktif' WHERE id_user = $id_user";
    $sql_unvalidasi = mysqli_query($conn, $query_unvalidasi);
    if ($sql_unvalidasi) {
        header('location: beranda.php');
        exit;
    }
}

// Ambil data user login
$id = $_SESSION['id_user'];
$query = "SELECT * FROM user NATURAL JOIN level WHERE id_user = $id";
$sql = mysqli_query($conn, $query);
$r = mysqli_fetch_array($sql);

// Definisi level dan hitung status aktif per level
$levels = [
    1 => "Administrator",
    2 => "Waiter",
    3 => "Kasir",
    4 => "Owner",
    5 => "Pelanggan"
];

$status_counts = [];
foreach ($levels as $id_level => $level_name) {
    $query = "SELECT COUNT(*) AS jumlah FROM user WHERE id_level = $id_level AND status = 'aktif'";
    $sql = mysqli_query($conn, $query);
    $status_counts[$level_name] = mysqli_fetch_array($sql)['jumlah'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <!-- Penting untuk responsive -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Beranda</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f1f5f9;
            overflow-x: hidden;
        }
        /* Sidebar styling untuk desktop */
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
        /* Content styling untuk desktop */
        .content {
            margin-left: 280px;
            padding: 20px;
            transition: margin-left 0.3s ease;
        }
        .content.shifted {
            margin-left: 20px;
        }
        /* Toggle button */
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
        /* Media Query untuk tampilan mobile */
        @media (max-width: 768px) {
            .sidebar {
                width: 250px;
                transform: translateX(-250px);
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .content {
                margin-left: 0;
            }
            .content.shifted {
                margin-left: 250px;
            }
            .toggle-btn {
                left: 10px;
            }
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

    <!-- Main Content -->
    <div class="content" id="content">
        <div class="container mt-4">
            <?php if ($r['id_level'] <= 3): ?>
                <div class="card shadow-sm">
                    <div class="card-header bg-info text-white">
                        <h5><i class="fas fa-users"></i> Data Pengguna</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Statistik Pengguna -->
                            <div class="col-md-4">
                                <div class="list-group">
                                    <?php foreach ($status_counts as $level_name => $count): ?>
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><i class="fas fa-user-circle"></i> <?= $level_name; ?></span>
                                            <span class="badge bg-primary rounded-pill"><?= $count; ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <!-- Tabel Data Pengguna -->
                            <div class="col-md-8">
                                <?php foreach ($levels as $id_level => $level_name):
                                    $query = "SELECT * FROM user WHERE id_level = $id_level";
                                    $sql = mysqli_query($conn, $query);
                                ?>
                                    <div class="card mb-3">
                                        <div class="card-header bg-success text-white">
                                            <h5>Data <?= $level_name; ?></h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>No.</th>
                                                            <th>Nama</th>
                                                            <th>Username</th>
                                                            <th>Status</th>
                                                            <th>Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $no = 1; while ($r_dt = mysqli_fetch_array($sql)): ?>
                                                            <tr>
                                                                <td class="text-center"><?= $no++; ?>.</td>
                                                                <td><?= $r_dt['nama_user']; ?></td>
                                                                <td><?= $r_dt['username']; ?></td>
                                                                <td>
                                                                    <span class="badge <?= $r_dt['status'] == 'aktif' ? 'bg-success' : 'bg-secondary'; ?>">
                                                                        <?= $r_dt['status']; ?>
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    <!-- Form aksi untuk update status dan hapus -->
                                                                    <form action="" method="post">
                                                                        <?php if ($r_dt['status'] == 'aktif'): ?>
                                                                            <button type="submit" name="unvalidasi" value="<?= $r_dt['id_user']; ?>" class="btn btn-warning btn-sm">
                                                                                <i class="fas fa-ban"></i> Nonaktifkan
                                                                            </button>
                                                                        <?php else: ?>
                                                                            <button type="submit" name="validasi" value="<?= $r_dt['id_user']; ?>" class="btn btn-success btn-sm">
                                                                                <i class="fas fa-check-circle"></i> Aktifkan
                                                                            </button>
                                                                            <button type="submit" name="hapus_user" value="<?= $r_dt['id_user']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                                                                <i class="fas fa-trash-alt"></i> Hapus
                                                                            </button>
                                                                        <?php endif; ?>
                                                                    </form>
                                                                </td>
                                                            </tr>
                                                        <?php endwhile; ?>
                                                    </tbody>
                                                </table>
                                            </div><!-- .table-responsive -->
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Fungsi toggle sidebar -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('content');
            const toggleBtn = document.querySelector('.toggle-btn');
            if (window.innerWidth < 768) {
                sidebar.classList.toggle('open');
                content.classList.toggle('shifted');
                toggleBtn.innerHTML = sidebar.classList.contains('open') ? '✖' : '☰';
            } else {
                sidebar.classList.toggle('closed');
                content.classList.toggle('shifted');
                toggleBtn.innerHTML = sidebar.classList.contains('closed') ? '☰' : '✖';
            }
        }
    </script>
</body>
</html>
