
<?php
session_start();
ob_start();

// Koneksi ke database
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'kasir_restoran';
$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die('Koneksi gagal: ' . mysqli_connect_error());
}

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['id_user'])) {
    die('Silakan login terlebih dahulu.');
}

$id = $_SESSION['id_user'];

// Ambil data user dari database
$query_user = "SELECT * FROM user NATURAL JOIN level WHERE id_user = $id";
$sql_user = mysqli_query($conn, $query_user);
$user_data = mysqli_fetch_assoc($sql_user);
$nama_user = $user_data['nama_user'];

// Notifikasi edit menu
if (isset($_SESSION['edit_menu'])) {
    echo $_SESSION['edit_menu'];
    unset($_SESSION['edit_menu']);
}

// Ambil daftar menu dari tabel masakan
$query_menu = "SELECT * FROM masakan";
$menu_result = mysqli_query($conn, $query_menu);

if (!$menu_result) {
    die('Query gagal: ' . mysqli_error($conn));
}

// Proses pemesanan makanan
if (isset($_POST['pesan'])) {
    $id_masakan = $_POST['id_masakan'];

    // Periksa stok
    $query_stok = "SELECT stok FROM masakan WHERE id_masakan = $id_masakan";
    $stok_result = mysqli_query($conn, $query_stok);
    $stok_data = mysqli_fetch_assoc($stok_result);

    if ($stok_data['stok'] > 0) {
        // Periksa apakah menu sudah ada di keranjang
        $query_cek_pesan = "SELECT * FROM tb_pesan WHERE id_user = $id AND id_masakan = $id_masakan";
        $cek_pesan_result = mysqli_query($conn, $query_cek_pesan);

        if (mysqli_num_rows($cek_pesan_result) > 0) {
            // Jika sudah ada, update jumlah pesanan
            $query_update_jumlah = "UPDATE tb_pesan SET jumlah = jumlah + 1 WHERE id_user = $id AND id_masakan = $id_masakan";
            mysqli_query($conn, $query_update_jumlah);
        } else {
            // Jika belum ada, masukkan pesanan baru
            $query_pesan = "INSERT INTO tb_pesan (id_user, id_masakan, jumlah) VALUES ($id, $id_masakan, 1)";
            mysqli_query($conn, $query_pesan);
        }

        // Kurangi stok
        $query_update_stok = "UPDATE masakan SET stok = stok - 1 WHERE id_masakan = $id_masakan";
        mysqli_query($conn, $query_update_stok);
    } else {
        echo "Stok habis!";
    }
}

// Ambil keranjang pesanan
$query_order = "SELECT p.id_pesan, m.nama_masakan, p.jumlah, m.harga FROM tb_pesan p JOIN masakan m ON p.id_masakan = m.id_masakan WHERE p.id_user = $id";
$order_fiks_result = mysqli_query($conn, $query_order);

// Hapus pesanan dari keranjang
if (isset($_POST['hapus_pesan'])) {
    $id_pesan = $_POST['hapus_pesan'];
    
    // Ambil data jumlah dari pesanan
    $query_jumlah = "SELECT id_masakan, jumlah FROM tb_pesan WHERE id_pesan = $id_pesan";
    $result_jumlah = mysqli_query($conn, $query_jumlah);
    $data_jumlah = mysqli_fetch_assoc($result_jumlah);

    // Kembalikan stok
    $query_kembalikan_stok = "UPDATE masakan SET stok = stok + {$data_jumlah['jumlah']} WHERE id_masakan = {$data_jumlah['id_masakan']}";
    mysqli_query($conn, $query_kembalikan_stok);

    // Hapus pesanan
    $query_hapus = "DELETE FROM tb_pesan WHERE id_pesan = $id_pesan";
    mysqli_query($conn, $query_hapus);
}

// Hitung total harga
$query_total_harga = "SELECT SUM(p.jumlah * m.harga) AS total_harga FROM tb_pesan p JOIN masakan m ON p.id_masakan = m.id_masakan WHERE p.id_user = $id";
$result_total_harga = mysqli_query($conn, $query_total_harga);
$total_harga_data = mysqli_fetch_assoc($result_total_harga);
$total_harga = $total_harga_data['total_harga'] ?? 0;

// Proses checkout
if (isset($_POST['checkout'])) {
    $uang_bayar = $_POST['uang_bayar'];
    $uang_kembali = $uang_bayar - $total_harga;

    if ($uang_bayar < $total_harga) {
        echo "Uang bayar kurang!";
    } else {
        // Simpan ke tabel order_pesanan
        $query_order_pesanan = "INSERT INTO order_pesanan (id_admin, id_pengunjung, waktu_pesan, total_harga, uang_bayar, uang_kembali, status_order) VALUES (1, $id, NOW(), $total_harga, $uang_bayar, $uang_kembali, 'sudah bayar')";
        if (mysqli_query($conn, $query_order_pesanan)) {
            $id_order = mysqli_insert_id($conn);

            // Hapus pesanan dari keranjang
            $query_tb_pesan = "SELECT * FROM tb_pesan WHERE id_user = $id";
            $pesan_result = mysqli_query($conn, $query_tb_pesan);
            while ($item = mysqli_fetch_assoc($pesan_result)) {
                $id_masakan = $item['id_masakan'];
                $jumlah = $item['jumlah'];

                // Update stok
                $query_update_stok = "UPDATE masakan SET stok = stok - $jumlah WHERE id_masakan = $id_masakan";
                mysqli_query($conn, $query_update_stok);
                
                // Hapus item dari keranjang
                $query_hapus_item = "DELETE FROM tb_pesan WHERE id_pesan = {$item['id_pesan']}";
                mysqli_query($conn, $query_hapus_item);
            }

            echo "Pesanan berhasil diselesaikan! Total harga: Rp " . number_format($total_harga) . ". Uang kembali: Rp " . number_format($uang_kembali);
        } else {
            echo "Terjadi kesalahan saat memproses pesanan.";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pemesanan</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
        }

        .dashboard-title-container {
            background-color: #007bff;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 0 0 10px 10px;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease-in-out;
        }

        .card:hover {
            transform: scale(1.05);
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .alert {
            border-radius: 8px;
        }

        .table {
            border-radius: 10px;
            overflow: hidden;
        }

        .logout-btn {
            position: absolute;
            top: 20px;
            right: 20px;
        }

        .food-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
        }

        .table th,
        .table td {
            vertical-align: middle;
        }
    </style>
</head>

<body>
    <div class="dashboard-title-container">
        <h1>Selamat Datang, <?= $nama_user; ?>!</h1>
        <p>Silakan pesan makanan favorit Anda!</p>
    </div>

    <div class="container my-4">
        <!-- Notifikasi -->
        <?php if (!empty($pesan)) : ?>
            <div class="alert alert-success text-center">
                <?= $pesan; ?>
            </div>
        <?php endif; ?>

        <!-- Daftar Menu -->
        <div class="row mb-4">
            <h2 class="text-center mb-4">Menu Makanan</h2>
            <?php while ($menu = mysqli_fetch_assoc($menu_result)) : ?>
                <div class="col-md-4 col-sm-6 mb-4">
                    <div class="card">
                        <img src="gambar/<?= $menu['gambar_masakan']; ?>" class="food-image" alt="<?= $menu['nama_masakan']; ?>">
                        <div class="card-body text-center">
                            <h5 class="card-title"><?= $menu['nama_masakan']; ?></h5>
                            <p class="card-text">Rp <?= number_format($menu['harga']); ?></p>
                            <form method="POST" action="">
                                <input type="hidden" name="id_masakan" value="<?= $menu['id_masakan']; ?>">
                                <button type="submit" name="pesan" class="btn btn-primary">Pesan</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Keranjang Pesanan -->
        <h2 class="text-center mb-4">Keranjang Pesanan</h2>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead class="table-primary text-center">
                    <tr>
                        <th>Nama</th>
                        <th>Jumlah</th>
                        <th>Harga</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    <?php while ($order = mysqli_fetch_assoc($order_fiks_result)) : ?>
                        <tr>
                            <td><?= $order['nama_masakan']; ?></td>
                            <td>
                                <form method="POST" action="">
                                    <div class="btn-group">
                                        <button type="submit" name="update_jumlah" value="decrease_<?= $order['id_pesan']; ?>" class="btn btn-secondary">-</button>
                                        <button type="submit" name="update_jumlah" value="increase_<?= $order['id_pesan']; ?>" class="btn btn-secondary">+</button>
                                    </div>
                                </form>
                            </td>
                            <td>Rp <?= number_format($order['harga'] * $order['jumlah']); ?></td>
                            <td>
                                <form method="POST" action="">
                                    <button type="submit" name="hapus_pesan" value="<?= $order['id_pesan']; ?>" class="btn btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    <tr>
                        <td colspan="2">Total Harga</td>
                        <td colspan="2">Rp <?= number_format($total_harga); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pembayaran -->
        <?php if ($total_harga > 0) : ?>
            <h2 class="text-center mb-4">Pembayaran</h2>
            <form method="POST" action="">
                <input type="hidden" name="total_harga" value="<?= $total_harga; ?>">
                <div class="mb-3">
                    <label for="uang_bayar" class="form-label">Uang Bayar</label>
                    <input type="number" name="uang_bayar" id="uang_bayar" class="form-control" required>
                </div>
                <button type="submit" name="checkout" class="btn btn-success w-100">Checkout</button>
            </form>
        <?php endif; ?>
    </div>
</body>

</html>

<?php
// Menutup koneksi
mysqli_close($conn);
?>
