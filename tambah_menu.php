<!DOCTYPE html>
<?php
include "connection/koneksi.php";
session_start();
ob_start();

$id = $_SESSION['id_user'];

if (isset($_SESSION['username'])) {

    $query = "SELECT * FROM user NATURAL JOIN level WHERE id_user = $id";
    $sql = mysqli_query($conn, $query);

    // Default values for menu fields
    $id_masakan = "";
    $nama_masakan = "";
    $harga = "";
    $stok = "";
    $gambar_masakan = "no_image.png";

    // Check if edit menu session is active
    if (isset($_SESSION['edit_menu'])) {
        $id = $_SESSION['edit_menu'];
        $query_data_edit = "SELECT * FROM masakan WHERE id_masakan = $id";
        $sql_data_edit = mysqli_query($conn, $query_data_edit);
        $result_data_edit = mysqli_fetch_array($sql_data_edit);

        $id_masakan = $result_data_edit['id_masakan'];
        $nama_masakan = $result_data_edit['nama_masakan'];
        $harga = $result_data_edit['harga'];
        $stok = $result_data_edit['stok'];
        $gambar_masakan = $result_data_edit['gambar_masakan'];
    }

    while ($r = mysqli_fetch_array($sql)) {
        $nama_user = $r['nama_user'];
?>
<html lang="en">
<head>
    <title>Entri Referensi</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="template/dashboard/css/matrix-style.css" />
    <style>
        body {
            background-color: #f8f9fa;
        }
        .form-container {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .form-title {
            font-weight: bold;
            text-align: center;
            color: #333;
        }
        .btn-custom {
            width: 100%;
        }
        #previewne {
            width: 150px;
            height: 100px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
<div class="container my-5">
    <div class="form-container">
        <h4 class="form-title mb-4">
            <?php echo isset($_SESSION['edit_menu']) ? 'Ubah Detail Menu' : 'Tambah Menu'; ?>
        </h4>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="nama_masakan" class="form-label">Nama Masakan</label>
                <input type="text" name="nama_masakan" id="nama_masakan" class="form-control" placeholder="Nama Masakan" value="<?php echo $nama_masakan; ?>" <?php echo isset($_SESSION['edit_menu']) ? 'readonly' : ''; ?> >
            </div>
            <div class="mb-3">
                <label for="harga" class="form-label">Harga / Porsi</label>
                <input type="number" name="harga" id="harga" class="form-control" placeholder="Rupiah" value="<?php echo $harga; ?>" min="0">
            </div>
            <div class="mb-3">
                <label for="stok" class="form-label">Stok Persediaan</label>
                <input type="number" name="stok" id="stok" class="form-control" placeholder="Jumlah Stok" value="<?php echo $stok; ?>" " min="0">
            </div>
            <div class="mb-3">
                <label for="gambar" class="form-label">Gambar Masakan</label>
                <input type="file" name="gambar" id="gambar" class="form-control" accept="image/*" onchange="preview(this,'previewne')">
                <img src="gambar/<?php echo $gambar_masakan; ?>" id="previewne" class="rounded border mt-2">
            </div>
            <div class="d-grid gap-2">
                <button type="submit" name="<?php echo isset($_SESSION['edit_menu']) ? 'ubah_menu' : 'tambah_menu'; ?>" class="btn btn-<?php echo isset($_SESSION['edit_menu']) ? 'info' : 'success'; ?> btn-custom">
                    <?php echo isset($_SESSION['edit_menu']) ? 'Simpan Perubahan' : 'Tambahkan'; ?>
                </button>
                <button type="submit" name="batal_menu" class="btn btn-danger btn-custom">Batalkan</button>
            </div>
        </form>
        <?php
        if (isset($_POST['tambah_menu']) || isset($_POST['ubah_menu'])) {
            $nama_masakan = $_POST['nama_masakan'];
            $harga = $_POST['harga'];
            $stok = $_POST['stok'];
            $status_masakan = $stok > 0 ? 'tersedia' : 'habis';

            $gambar = $gambar_masakan; // Default to existing image
            if (!empty($_FILES['gambar']['name'])) {
                $direktori = "gambar/";
                $tmp_name = $_FILES['gambar']['tmp_name'];
                $name = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
                $gambar = $nama_masakan . "." . $name;
                move_uploaded_file($tmp_name, $direktori . $gambar);
            }

            if (isset($_POST['tambah_menu'])) {
                $query_tambah = "INSERT INTO masakan (nama_masakan, harga, stok, status_masakan, gambar_masakan) 
                                 VALUES ('$nama_masakan', '$harga', '$stok', '$status_masakan', '$gambar')";
                if (mysqli_query($conn, $query_tambah)) {
                    header('Location: entri_referensi.php');
                }
            } elseif (isset($_POST['ubah_menu'])) {
                $query_ubah = "UPDATE masakan SET 
                               harga = '$harga', 
                               stok = '$stok', 
                               status_masakan = '$status_masakan', 
                               gambar_masakan = '$gambar' 
                               WHERE id_masakan = '$id_masakan'";
                if (mysqli_query($conn, $query_ubah)) {
                    unset($_SESSION['edit_menu']);
                    header('Location: entri_referensi.php');
                }
            }
        }

        if (isset($_POST['batal_menu'])) {
            unset($_SESSION['edit_menu']);
            header('Location: entri_referensi.php');
        }
        ?>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function preview(gambar, idpreview) {
        var gb = gambar.files;
        for (var i = 0; i < gb.length; i++) {
            var gbPreview = gb[i];
            var imageType = /image.*/;
            var preview = document.getElementById(idpreview);
            var reader = new FileReader();
            if (gbPreview.type.match(imageType)) {
                reader.onload = function(e) { preview.src = e.target.result; };
                reader.readAsDataURL(gbPreview);
            } else {
                alert("File harus berupa gambar.");
            }
        }
    }
</script>
</body>
</html>
<?php
    }
} else {
    header('Location: logout.php');
}
ob_flush();
?>
