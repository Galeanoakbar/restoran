<?php
include "connection/koneksi.php";
session_start();
ob_start();

$id = $_SESSION['id_user'];

if (isset($_SESSION['edit_menu'])) {
    echo $_SESSION['edit_menu'];
    unset($_SESSION['edit_menu']);
}

if (isset($_SESSION['username'])) {
    $query = "SELECT * FROM user NATURAL JOIN level WHERE id_user = $id";
    mysqli_query($conn, $query);
    $sql = mysqli_query($conn, $query);

    while ($r = mysqli_fetch_array($sql)) {
        $nama_user = $r['nama_user'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Open Sans', sans-serif;
        }
        .card-header {
            background-color: #007bff;
            color: white;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
    </style>
</head>
<body>
<div class="container my-4">
    <div class="row">
        <?php if ($r['id_level'] == 1) {
            $id_order = $_SESSION['edit_order'];
            $query_pemesan = "SELECT * FROM order_pesanan LEFT JOIN user ON order_pesanan.id_pengunjung = user.id_user WHERE id_order = $id_order";
            $sql_pemesan = mysqli_query($conn, $query_pemesan);
            $result_pemesan = mysqli_fetch_array($sql_pemesan);
            $id_pemesan = $result_pemesan['id_pengunjung'];
        ?>
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5>Transaksi Pembayaran (<?php echo $result_pemesan['nama_user']; ?>)</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Menu</th>
                                <th>Jumlah</th>
                                <th>Harga</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no_order_fiks = 1;
                            $query_order_fiks = "SELECT * FROM tb_pesan LEFT JOIN masakan ON tb_pesan.id_masakan = masakan.id_masakan WHERE id_user = $id_pemesan AND status_pesan != 'sudah'";
                            $sql_order_fiks = mysqli_query($conn, $query_order_fiks);
                            while ($r_order_fiks = mysqli_fetch_array($sql_order_fiks)) {
                            ?>
                            <tr>
                                <td><?php echo $no_order_fiks++; ?></td>
                                <td><?php echo $r_order_fiks['nama_masakan']; ?></td>
                                <td><?php echo $r_order_fiks['jumlah']; ?></td>
                                <td>Rp. <?php echo $r_order_fiks['harga']; ?>,-</td>
                                <td>Rp. <?php echo $r_order_fiks['harga'] * $r_order_fiks['jumlah']; ?>,-</td>
                            </tr>
                            <?php } ?>
                            <?php
                            $query_harga = "SELECT * FROM order_pesanan WHERE id_pengunjung = $id_pemesan AND status_order = 'belum bayar'";
                            $sql_harga = mysqli_query($conn, $query_harga);
                            $result_harga = mysqli_fetch_array($sql_harga);
                            ?>
                            <tr>
                                <td colspan="4" class="text-end"><strong>Total</strong></td>
                                <td><strong>Rp. <?php echo $result_harga['total_harga']; ?>,-</strong></td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-end"><strong>No. Meja</strong></td>
                                <td><strong><?php echo $result_harga['no_meja']; ?></strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <form action="#" method="post">
                        <div class="mb-3">
                            <label for="uang_bayar" class="form-label">Membayar: Rp.</label>
                            <input type="number" id="uang_bayar" name="uang_bayar" class="form-control" onchange="return operasi()">
                        </div>
                        <div class="mb-3">
                            <label for="uang_kembali" class="form-label">Kembalian: Rp.</label>
                            <input type="number" id="uang_kembali1" class="form-control" disabled>
                            <input type="hidden" id="uang_kembali" name="uang_kembali">
                        </div>
                        <div class="text-center">
                            <button type="submit" value="<?php echo $result_harga['id_order']; ?>" name="save_order" class="btn btn-primary">
                                <i class="fas fa-print"></i> Transaksi Selesai
                            </button>
                            <button type="submit" name="back_order" class="btn btn-danger">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php
        if (isset($_REQUEST['back_order'])) {
            if (isset($_SESSION['edit_order'])) {
                unset($_SESSION['edit_order']);
                header('location: entri_transaksi.php');
            }
        }

        if (isset($_REQUEST['save_order'])) {
            if (isset($_SESSION['edit_order'])) {
                unset($_SESSION['edit_order']);
            }
            $uang_bayar = $_POST['uang_bayar'];
            $uang_kembali = $_POST['uang_kembali'];
            $query_save_transaksi = "UPDATE order_pesanan SET id_admin = $id, uang_bayar = $uang_bayar, uang_kembali = $uang_kembali, status_order = 'sudah bayar' WHERE id_order = $id_order";
            $sql_save_transaksi = mysqli_query($conn, $query_save_transaksi);

            $query_selesai_pesan = "UPDATE tb_pesan SET status_pesan = 'sudah' WHERE id_user = $id_pemesan AND status_pesan != 'sudah'";
            $sql_selesai_pesan = mysqli_query($conn, $query_selesai_pesan);
            if ($sql_selesai_pesan) {
                echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Pembayaran Berhasil!',
                        text: 'Transaksi telah selesai.',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = 'entri_transaksi.php';
                    });
                </script>";
            }
        }
        ?>
        <?php } ?>
    </div>
</div>
<script>
    function operasi() {
        var total_biaya = <?php echo $result_harga['total_harga']; ?>;
        var uang_bayar = document.getElementById("uang_bayar").value;
        var kembalian = uang_bayar - total_biaya;
        if (kembalian < 0) {
            Swal.fire({
                icon: 'error',
                title: 'Uang Tidak Cukup',
                text: 'Uang pembayaran kurang!',
                confirmButtonText: 'OK'
            });
            return false;
        }
        document.getElementById("uang_kembali1").value = kembalian;
        document.getElementById("uang_kembali").value = kembalian;
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
    }
} else {
    header('location: logout.php');
}
ob_flush();
?>
