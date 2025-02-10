<!DOCTYPE html>
<?php
include "connection/koneksi.php";
session_start();
ob_start();

// Jika ada parameter GET 'edit', set mode edit
if (isset($_GET['edit'])) {
    $_SESSION['edit_menu'] = $_GET['edit'];
}

$id = $_SESSION['id_user'];

if (isset($_SESSION['username'])) {

    // Ambil data user (misalnya untuk menampilkan nama di header)
    $query = "SELECT * FROM user NATURAL JOIN level WHERE id_user = $id";
    $sql = mysqli_query($conn, $query);

    // Nilai default untuk form menu
    $id_masakan = "";
    $nama_masakan = "";
    $harga = "";
    $stok = "";
    $gambar_masakan = "no_image.png";

    // Jika mode edit aktif, ambil data menu yang akan diedit
    if (isset($_SESSION['edit_menu'])) {
        $id_edit = $_SESSION['edit_menu'];
        $query_data_edit = "SELECT * FROM masakan WHERE id_masakan = $id_edit";
        $sql_data_edit = mysqli_query($conn, $query_data_edit);
        if($result_data_edit = mysqli_fetch_array($sql_data_edit)){
            $id_masakan = $result_data_edit['id_masakan'];
            $nama_masakan = $result_data_edit['nama_masakan'];
            $harga = $result_data_edit['harga'];
            $stok = $result_data_edit['stok'];
            $gambar_masakan = $result_data_edit['gambar_masakan'];
        }
    }

    // Ambil data user (asumsi hanya satu record)
    while ($r = mysqli_fetch_array($sql)) {
        $nama_user = $r['nama_user'];
?>
<html lang="en">
<head>
    <title><?php echo isset($_SESSION['edit_menu']) ? 'Ubah Detail Menu' : 'Tambah Menu'; ?></title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" />
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f1f5f9;
            font-family: 'Roboto', sans-serif;
        }
        .form-container {
            max-width: 600px;
            margin: 50px auto;
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }
        .form-title {
            font-weight: 700;
            margin-bottom: 20px;
            text-align: center;
            color: #333;
        }
        .btn-custom {
            width: 100%;
            padding: 10px;
            font-size: 1.1rem;
        }
        #previewne {
            width: 200px;
            height: auto;
            max-height: 150px;
            margin-top: 10px;
            border: 2px solid #ddd;
            border-radius: 8px;
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #17a2b8;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="form-container">
        <h4 class="form-title mb-4">
            <?php echo isset($_SESSION['edit_menu']) ? 'Ubah Detail Menu' : 'Tambah Menu'; ?>
        </h4>
        <form action="" method="post" enctype="multipart/form-data" id="menuForm">
            <div class="mb-3">
                <label for="nama_masakan" class="form-label">Nama Masakan</label>
                <!-- Jika dalam mode edit, Anda bisa memilih apakah field nama ingin readonly atau dapat diubah -->
                <input type="text" name="nama_masakan" id="nama_masakan" class="form-control" placeholder="Nama Masakan" value="<?php echo $nama_masakan; ?>" required>
            </div>
            <div class="mb-3">
                <label for="harga" class="form-label">Harga / Porsi</label>
                <input type="number" name="harga" id="harga" class="form-control" placeholder="Rupiah" value="<?php echo $harga; ?>" min="0" required>
            </div>
            <div class="mb-3">
                <label for="stok" class="form-label">Stok Persediaan</label>
                <input type="number" name="stok" id="stok" class="form-control" placeholder="Jumlah Stok" value="<?php echo $stok; ?>" min="0" required>
            </div>
            <div class="mb-3">
                <label for="gambar" class="form-label">Gambar Masakan</label>
                <input type="file" name="gambar" id="gambar" class="form-control" accept="image/*" onchange="preview(this, 'previewne')">
                <img src="gambar/<?php echo $gambar_masakan; ?>" id="previewne" class="mt-3">
            </div>
            <div class="d-grid gap-2">
                <button type="submit" name="<?php echo isset($_SESSION['edit_menu']) ? 'ubah_menu' : 'tambah_menu'; ?>" class="btn btn-<?php echo isset($_SESSION['edit_menu']) ? 'info' : 'success'; ?> btn-custom">
                    <?php echo isset($_SESSION['edit_menu']) ? 'Simpan Perubahan' : 'Tambahkan'; ?>
                </button>
                <button type="button" id="cancelBtn" class="btn btn-danger btn-custom">Batalkan</button>
            </div>
        </form>
    </div>
</div>

<!-- Toast Notification (opsional) -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div id="liveToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                Proses berhasil!
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Fungsi untuk preview gambar secara real-time
    function preview(input, idpreview) {
        var preview = document.getElementById(idpreview);
        var file = input.files[0];
        if (file && file.type.startsWith("image/")) {
            var reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
            }
            reader.readAsDataURL(file);
        } else {
            alert("File harus berupa gambar.");
            input.value = "";
        }
    }

    // Konfirmasi pembatalan
    document.getElementById('cancelBtn').addEventListener('click', function(){
        if(confirm("Apakah anda yakin ingin membatalkan?")){
            // Jika dalam mode edit, unset session edit_menu
            <?php if(isset($_SESSION['edit_menu'])) { ?>
                <?php unset($_SESSION['edit_menu']); ?>
            <?php } ?>
            window.location.href = "entri_referensi.php";
        }
    });

    // Menampilkan toast notifikasi saat form disubmit (bila diperlukan)
    document.getElementById('menuForm').addEventListener('submit', function(e){
        var toastEl = document.getElementById('liveToast');
        var toast = new bootstrap.Toast(toastEl);
        toast.show();
    });
</script>
</body>
</html>
<?php
    } // akhir while ($r = mysqli_fetch_array($sql))
} else {
    header('Location: logout.php');
    exit;
}

// Proses penambahan atau pengubahan menu
if(isset($_POST['tambah_menu']) || isset($_POST['ubah_menu'])){
    $nama_masakan = $_POST['nama_masakan'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    $status_masakan = $stok > 0 ? 'tersedia' : 'habis';

    // Gunakan gambar default jika tidak ada file yang diupload
    // Jika tidak ada file baru, gunakan gambar yang sebelumnya tersimpan (pada mode edit)
    $gambar = $gambar_masakan;
    if (!empty($_FILES['gambar']['name'])) {
        $direktori = "gambar/";
        $tmp_name = $_FILES['gambar']['tmp_name'];
        $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        // Buat nama file berdasarkan nama masakan (pastikan sudah di-filter jika diperlukan)
        $gambar = $nama_masakan . "." . $ext;
        move_uploaded_file($tmp_name, $direktori . $gambar);
    }

    if (isset($_POST['tambah_menu'])) {
        $query_tambah = "INSERT INTO masakan (nama_masakan, harga, stok, status_masakan, gambar_masakan) 
                         VALUES ('$nama_masakan', '$harga', '$stok', '$status_masakan', '$gambar')";
        if (mysqli_query($conn, $query_tambah)) {
            header('Location: entri_referensi.php');
            exit;
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } elseif (isset($_POST['ubah_menu'])) {
        $query_ubah = "UPDATE masakan SET
                       nama_masakan = '$nama_masakan', 
                       harga = '$harga', 
                       stok = '$stok', 
                       status_masakan = '$status_masakan', 
                       gambar_masakan = '$gambar' 
                       WHERE id_masakan = '$id_masakan'";
        if (mysqli_query($conn, $query_ubah)) {
            // Setelah update, unset session edit menu agar form kembali ke mode tambah
            unset($_SESSION['edit_menu']);
            header('Location: entri_referensi.php');
            exit;
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}

// Jika pengguna menekan tombol batal (jika ada mekanisme lain)
// Anda juga bisa menangani unset session edit_menu di sini jika diperlukan
ob_flush();
?>
