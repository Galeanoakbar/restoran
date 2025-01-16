<!DOCTYPE html>
<?php
include "connection/koneksi.php";
session_start();
ob_start();

$id = $_SESSION['id_user'] ?? null;

if (isset($_SESSION['edit_menu'])) {
    echo $_SESSION['edit_menu'];
    unset($_SESSION['edit_menu']);
}

if (isset($_SESSION['username'])) {
    $query = "SELECT * FROM user NATURAL JOIN level WHERE id_user = $id";
    $sql = mysqli_query($conn, $query);

    if ($sql && $user = mysqli_fetch_array($sql)) {
        $nama_user = $user['nama_user'];
?>
<html lang="en">
<head>
    <title>Entri Referensi</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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

        /* Content Styles */
        .content {
            margin-left: 300px;
            padding: 30px;
        }

        /* Card Styles */
        .card {
            border: none;
            border-radius: 10px;
            background: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }

        .card:hover {
            transform: scale(1.02);
        }

        .card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .card-body {
            padding: 20px;
            text-align: center;
        }

        .btn {
            font-size: 0.9rem;
            padding: 10px 15px;
            border-radius: 5px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 250px;
            }

            .content {
                margin-left: 250px;
            }
        }
    </style>
</head>
<body>
<body>
    <button class="toggle-btn" onclick="toggleSidebar()">☰ Menu</button>
    <div class="sidebar" id="sidebar">
        <h3>Welcome, <?php echo htmlspecialchars($nama_user); ?></h3>
        <a href="beranda.php"><i class="fas fa-home"></i> Beranda</a>
        <a href="entri_referensi.php"><i class="fas fa-utensils"></i> Entri Referensi</a>
        <a href="entri_order.php"><i class="fas fa-shopping-cart"></i> Entri Order</a>
        <a href="entri_transaksi.php"><i class="fas fa-money-bill"></i> Entri Transaksi</a>
        <a href="generate_laporan.php"><i class="fas fa-print"></i> Generate Laporan</a>
        <a href="logout.php" class="btn btn-danger w-100 mt-3"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <!-- Content -->
    <div class="content">
        <h1 class="text-center mb-4">Referensi Makanan</h1>
        <div class="text-end mb-3">
            <a href="tambah_menu.php" class="btn btn-primary">Tambah Data</a>
        </div>
        <div class="row">
            <?php
            $query_data_makanan = "SELECT * FROM masakan ORDER BY id_masakan DESC";
            $sql_data_makanan = mysqli_query($conn, $query_data_makanan);

            while ($makanan = mysqli_fetch_array($sql_data_makanan)) {
                $id_masakan = $makanan['id_masakan'];
                $nama_masakan = $makanan['nama_masakan'];
                $gambar_masakan = $makanan['gambar_masakan'];
                $harga = $makanan['harga'];
                $stok = $makanan['stok'];
            ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <img src="gambar/<?php echo htmlspecialchars($gambar_masakan); ?>" alt="<?php echo htmlspecialchars($nama_masakan); ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($nama_masakan); ?></h5>
                        <p class="card-text">
                            <strong>Harga:</strong> Rp. <?php echo number_format($harga); ?>,-<br>
                            <strong>Stok:</strong> <?php echo htmlspecialchars($stok); ?> Porsi
                        </p>
                        <form action="" method="post">
                            <button type="submit" name="edit_menu" value="<?php echo $id_masakan; ?>" class="btn btn-success btn-sm">Edit</button>
                            <button type="submit" name="hapus_menu" value="<?php echo $id_masakan; ?>" class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
            <?php
            }

            if (isset($_POST['hapus_menu'])) {
                $id_masakan = $_POST['hapus_menu'];

                $query_lihat = "SELECT * FROM masakan WHERE id_masakan = $id_masakan";
                $sql_lihat = mysqli_query($conn, $query_lihat);
                $result_lihat = mysqli_fetch_array($sql_lihat);

                if (file_exists('gambar/' . $result_lihat['gambar_masakan'])) {
                    unlink('gambar/' . $result_lihat['gambar_masakan']);
                }

                $query_hapus_masakan = "DELETE FROM masakan WHERE id_masakan = $id_masakan";
                mysqli_query($conn, $query_hapus_masakan);

                header('location: entri_referensi.php');
            }

            if (isset($_POST['edit_menu'])) {
                $_SESSION['edit_menu'] = $_POST['edit_menu'];
                header('location: tambah_menu.php');
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
