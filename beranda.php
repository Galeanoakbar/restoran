<?php
include "connection/koneksi.php";
session_start();
ob_start();

if (!isset($_SESSION['username'])) {
    header('location: logout.php');
    exit;
}

$id = $_SESSION['id_user'];
$query = "SELECT * FROM user NATURAL JOIN level WHERE id_user = $id";
$sql = mysqli_query($conn, $query);
$r = mysqli_fetch_array($sql);

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Beranda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="template/dashboard/css/custom.css" />
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f1f5f9;
            overflow-x: hidden;
        }

        /* Sidebar styling */
        .sidebar {
            height: 100vh;
            width: 280px;
            background: #212529;
            color: #fff;
            position: fixed;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
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

        /* Content styling */
        .content {
            margin-left: 280px;
            padding: 20px;
            transition: margin-left 0.3s ease;
        }

        .content.shifted {
            margin-left: 20px;
        }

        /* Button to toggle sidebar */
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
<!-- Toggle Button -->
<button class="toggle-btn" onclick="toggleSidebar()">☰ Menu</button>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <h3>Welcome, <?php echo htmlspecialchars($r['nama_user']); ?></h3>
    <ul>
        <?php
        if ($r['id_level'] == 1) { // Administrator
        ?>
            <a href="beranda.php"><i class="fas fa-home"></i> Beranda</a>
            <a href="entri_referensi.php"><i class="fas fa-utensils"></i> Entri Referensi</a>
            <a href="entri_order.php"><i class="fas fa-shopping-cart"></i> Entri Order</a>
            <a href="entri_transaksi.php"><i class="fas fa-money-bill"></i> Entri Transaksi</a>
            <a href="generate_laporan.php"><i class="fas fa-print"></i> Generate Laporan</a>
        <?php
        } elseif ($r['id_level'] == 2) { // Waiter
        ?>
            <a href="beranda.php"><i class="fas fa-home"></i> Beranda</a>
            <a href="entri_order.php"><i class="fas fa-shopping-cart"></i> Entri Order</a>
            <a href="generate_laporan.php"><i class="fas fa-print"></i> Generate Laporan</a>
        <?php
        } elseif ($r['id_level'] == 3) { // Kasir
        ?>
            <a href="beranda.php"><i class="fas fa-home"></i> Beranda</a>
            <a href="entri_transaksi.php"><i class="fas fa-money-bill"></i> Entri Transaksi</a>
            <a href="generate_laporan.php"><i class="fas fa-print"></i> Generate Laporan</a>
        <?php
        } elseif ($r['id_level'] == 4) { // Owner
        ?>
            <a href="beranda.php"><i class="fas fa-home"></i> Beranda</a>
            <a href="generate_laporan.php"><i class="fas fa-print"></i> Generate Laporan</a>
        <?php
        } elseif ($r['id_level'] == 5) { // Pelanggan
        ?>
            <a href="entri_order.php"><i class="fas fa-shopping-cart"></i> Entri Order</a>
        <?php
        }
        ?>
        <a href="logout.php" class="btn btn-danger w-100 mt-3"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </ul>
</div>

<!-- Main Content -->
<div class="content" id="content">
    <div class="container mt-4">
        <div class="row">
            <?php if ($r['id_level'] <= 3) { ?>
                <div class="col-md-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-info text-white">
                            <h5><i class="fas fa-users"></i> Data Pengguna</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- User Stats -->
                                <div class="col-md-4">
                                    <div class="list-group">
                                        <?php foreach ($status_counts as $level_name => $count) { ?>
                                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                                <span><i class="fas fa-user-circle"></i> <?= $level_name; ?></span>
                                                <span class="badge bg-primary rounded-pill"><?= $count; ?></span>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <!-- Data Tabels -->
                                <div class="col-md-8">
                                    <?php foreach ($levels as $id_level => $level_name) { 
                                        $query = "SELECT * FROM user WHERE id_level = $id_level";
                                        $sql = mysqli_query($conn, $query); ?>
                                        <div class="card mb-3">
                                            <div class="card-header bg-success text-white">
                                                <h5>Data <?= $level_name; ?></h5>
                                            </div>
                                            <div class="card-body">
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
                                                        <?php $no = 1; while ($r_dt = mysqli_fetch_array($sql)) { ?>
                                                            <tr>
                                                                <td><center><?= $no++; ?>.</center></td>
                                                                <td><?= $r_dt['nama_user']; ?></td>
                                                                <td><?= $r_dt['username']; ?></td>
                                                                <td><span class="badge <?= $r_dt['status'] == 'aktif' ? 'bg-success' : 'bg-secondary'; ?>"><?= $r_dt['status']; ?></span></td>
                                                                <td>
                                                                    <form action="" method="post">
                                                                        <?php if ($r_dt['status'] == 'aktif') { ?>
                                                                            <button name="unvalidasi" value="<?= $r_dt['id_user']; ?>" class="btn btn-warning btn-sm"><i class="fas fa-ban"></i> Nonaktifkan</button>
                                                                        <?php } else { ?>
                                                                            <button name="validasi" value="<?= $r_dt['id_user']; ?>" class="btn btn-success btn-sm"><i class="fas fa-check-circle"></i> Aktifkan</button>
                                                                            <button name="hapus_user" value="<?= $r_dt['id_user']; ?>" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i> Hapus</button>
                                                                        <?php } ?>
                                                                    </form>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<!-- Modal untuk konfirmasi hapus -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus data ini?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger">Hapus</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Skrip untuk toggle sidebar
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
// Handle form submissions for user status update and deletion
if (isset($_POST['hapus_user'])) {
    $id_user = $_POST['hapus_user'];
    $query = "DELETE FROM user WHERE id_user = $id_user";
    mysqli_query($conn, $query);
    header('location: beranda.php');
    exit;
}

if (isset($_POST['validasi'])) {
    $id_user = $_POST['validasi'];
    $query = "UPDATE user SET status = 'aktif' WHERE id_user = $id_user";
    mysqli_query($conn, $query);
    header('location: beranda.php');
    exit;
}

if (isset($_POST['unvalidasi'])) {
    $id_user = $_POST['unvalidasi'];
    $query = "UPDATE user SET status = 'nonaktif' WHERE id_user = $id_user";
    mysqli_query($conn, $query);
    header('location: beranda.php');
    exit;
}
ob_flush();
