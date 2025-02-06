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
    <title>Beranda Modern</title>
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
        /* Sidebar styling */
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
        /* Content styling */
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
        /* Style untuk toast notifikasi */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1100;
        }
    </style>
</head>
<body>
    <!-- Toggle Button -->
    <button class="toggle-btn" onclick="toggleSidebar()">☰ Menu</button>

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

    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>

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
                                                        <tr id="row-<?= $r_dt['id_user']; ?>">
                                                            <td class="text-center"><?= $no++; ?>.</td>
                                                            <td><?= $r_dt['nama_user']; ?></td>
                                                            <td><?= $r_dt['username']; ?></td>
                                                            <td>
                                                                <span class="badge <?= $r_dt['status'] == 'aktif' ? 'bg-success' : 'bg-secondary'; ?>" id="status-<?= $r_dt['id_user']; ?>">
                                                                    <?= $r_dt['status']; ?>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <div class="btn-group" role="group">
                                                                    <?php if ($r_dt['status'] == 'aktif'): ?>
                                                                        <button class="btn btn-warning btn-sm" onclick="updateStatus(<?= $r_dt['id_user']; ?>, 'nonaktif')">
                                                                            <i class="fas fa-ban"></i> Nonaktifkan
                                                                        </button>
                                                                    <?php else: ?>
                                                                        <button class="btn btn-success btn-sm" onclick="updateStatus(<?= $r_dt['id_user']; ?>, 'aktif')">
                                                                            <i class="fas fa-check-circle"></i> Aktifkan
                                                                        </button>
                                                                        <button class="btn btn-danger btn-sm" onclick="confirmDelete(<?= $r_dt['id_user']; ?>)">
                                                                            <i class="fas fa-trash-alt"></i> Hapus
                                                                        </button>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php endwhile; ?>
                                                </tbody>
                                            </table>
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

    <!-- Modal Konfirmasi Hapus -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
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
            <button type="button" class="btn btn-danger" id="deleteConfirmBtn">Hapus</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Script untuk interaksi -->
    <script>
        // Toggle Sidebar
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('content');
            sidebar.classList.toggle('closed');
            content.classList.toggle('shifted');
        }

        // Fungsi untuk menampilkan toast notifikasi
        function showToast(message, type = 'success') {
            const toastContainer = document.getElementById('toastContainer');
            const toastEl = document.createElement('div');
            toastEl.className = `toast align-items-center text-bg-${type} border-0 show mb-2`;
            toastEl.setAttribute('role', 'alert');
            toastEl.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            `;
            toastContainer.appendChild(toastEl);
            setTimeout(() => {
                toastEl.classList.remove('show');
                setTimeout(() => { toastEl.remove(); }, 500);
            }, 3000);
        }

        // Update status pengguna via AJAX (Fetch API)
        function updateStatus(id, newStatus) {
            fetch('action_user.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `action=update&user_id=${id}&status=${newStatus}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update tampilan status pada tabel
                    document.getElementById('status-' + id).textContent = newStatus;
                    document.getElementById('status-' + id).className = 'badge ' + (newStatus === 'aktif' ? 'bg-success' : 'bg-secondary');
                    showToast(data.message);
                } else {
                    showToast(data.message, 'danger');
                }
            })
            .catch(() => showToast('Terjadi kesalahan!', 'danger'));
        }

        // Konfirmasi hapus data
        let deleteUserId = null;
        function confirmDelete(id) {
            deleteUserId = id;
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        }

        // Setelah konfirmasi hapus
        document.getElementById('deleteConfirmBtn').addEventListener('click', function() {
            if (deleteUserId) {
                fetch('action_user.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `action=delete&user_id=${deleteUserId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Hapus baris dari tabel
                        const row = document.getElementById('row-' + deleteUserId);
                        if(row) row.remove();
                        showToast(data.message);
                    } else {
                        showToast(data.message, 'danger');
                    }
                })
                .catch(() => showToast('Terjadi kesalahan!', 'danger'))
                .finally(() => {
                    // Sembunyikan modal
                    const modalEl = document.getElementById('deleteModal');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    modal.hide();
                });
            }
        });
    </script>
</body>
</html>
