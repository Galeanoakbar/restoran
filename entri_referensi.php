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
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Animate.css untuk animasi -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <!-- SweetAlert2 CSS untuk dialog interaktif -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f1f5f9;
            overflow-x: hidden;
        }
        /* Sidebar */
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
        /* Konten Utama */
        .content {
            margin-left: 300px;
            padding: 20px;
            transition: margin-left 0.3s ease;
        }
        /* Saat sidebar ditutup, konten diperluas */
        .content.expanded {
            margin-left: 20px;
        }
        /* Tombol Toggle */
        .toggle-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1100;
            background-color: #17a2b8;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 10px 15px;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        /* Tampilan Card */
        .card {
            border: none;
            border-radius: 10px;
            background: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .card:hover {
            transform: scale(1.02);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
        .card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            transition: transform 0.5s;
        }
        .card:hover img {
            transform: scale(1.1);
        }
        /* Favorite Button */
        .favorite-btn {
            background: transparent;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
            transition: color 0.3s, transform 0.3s;
        }
        .favorite-btn.favorited {
            color: red;
        }
        /* Interaktivitas tambahan pada menu makanan saat sidebar ditutup */
        .content.expanded .menu-item {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .content.expanded .menu-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }
        @media (max-width: 768px) {
            .sidebar {
                width: 250px;
            }
            .content {
                margin-left: 250px;
            }
            .content.expanded {
                margin-left: 20px;
            }
        }
        /* Sidebar tertutup */
        .sidebar.closed {
            transform: translateX(-280px);
        }
        /* Toast positioning */
        #toastContainer {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1200;
        }
    </style>
</head>
<body>
    <!-- Tombol toggle sidebar -->
    <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
    
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <h3>Welcome, <?php echo htmlspecialchars($nama_user); ?></h3>
        <ul class="list-unstyled">
            <?php if ($user['id_level'] == 1): // Administrator ?>
                <li><a href="beranda.php"><i class="fas fa-home"></i> Beranda</a></li>
                <li><a href="entri_referensi.php"><i class="fas fa-utensils"></i> Entri Referensi</a></li>
                <li><a href="entri_order.php"><i class="fas fa-shopping-cart"></i> Entri Order</a></li>
                <li><a href="entri_transaksi.php"><i class="fas fa-money-bill"></i> Entri Transaksi</a></li>
                <li><a href="generate_laporan.php"><i class="fas fa-print"></i> Generate Laporan</a></li>
            <?php elseif ($user['id_level'] == 2): // Waiter ?>
                <li><a href="entri_order.php"><i class="fas fa-shopping-cart"></i> Entri Order</a></li>
                <li><a href="generate_laporan.php"><i class="fas fa-print"></i> Generate Laporan</a></li>
            <?php elseif ($user['id_level'] == 3): // Kasir ?>
                <li><a href="entri_transaksi.php"><i class="fas fa-money-bill"></i> Entri Transaksi</a></li>
                <li><a href="generate_laporan.php"><i class="fas fa-print"></i> Generate Laporan</a></li>
            <?php elseif ($user['id_level'] == 4): // Owner ?>
                <li><a href="generate_laporan.php"><i class="fas fa-print"></i> Generate Laporan</a></li>
            <?php endif; ?>
            <li class="mt-3">
                <a href="logout.php" class="btn btn-danger w-100">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
    </div>
    
    <!-- Konten Utama -->
    <div class="content" id="content">
        <h1 class="text-center mb-4 animate__animated animate__fadeInDown">Menu Makanan</h1>
        
        <!-- Sorting dan Pencarian -->
        <div class="row mb-3">
            <div class="col-md-4">
                <select id="sortMenu" class="form-select">
                    <option value="default">Urutkan Berdasarkan</option>
                    <option value="name-asc">Nama (A-Z)</option>
                    <option value="price-asc">Harga (Rendah ke Tinggi)</option>
                    <option value="price-desc">Harga (Tinggi ke Rendah)</option>
                </select>
            </div>
            <div class="col-md-8">
                <input type="text" id="menuSearch" class="form-control" placeholder="Cari menu makanan..." />
            </div>
        </div>
        
        <div class="text-end mb-3">
            <a href="tambah_menu.php" class="btn btn-primary">Tambah Data</a>
        </div>
        <div class="row" id="menuContainer">
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
            <div class="col-md-4 mb-4 menu-item animate__animated animate__fadeInUp" data-name="<?php echo strtolower($nama_masakan); ?>" data-price="<?php echo $harga; ?>">
                <div class="card">
                    <img src="gambar/<?php echo htmlspecialchars($gambar_masakan); ?>" alt="<?php echo htmlspecialchars($nama_masakan); ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($nama_masakan); ?></h5>
                        <p class="card-text">
                            <strong>Harga:</strong> Rp. <?php echo number_format($harga); ?>,-<br>
                            <strong>Stok:</strong> <?php echo htmlspecialchars($stok); ?> Porsi
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <a href="tambah_menu.php?edit=<?php echo $id_masakan; ?>" class="btn btn-success btn-sm" data-bs-toggle="tooltip" title="Edit Menu">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <button class="btn btn-danger btn-sm" onclick="confirmDelete(<?php echo $id_masakan; ?>, '<?php echo htmlspecialchars($gambar_masakan); ?>')" data-bs-toggle="tooltip" title="Hapus Menu">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </div>
                            <!-- Favorite Button -->
                            <button class="favorite-btn" data-favorited="false" onclick="toggleFavorite(this)" data-bs-toggle="tooltip" title="Tambahkan ke Favorit">
                                <i class="far fa-heart"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
    
    <!-- Toast Container -->
    <div id="toastContainer" class="toast-container position-fixed bottom-0 end-0 p-3">
      <div id="favoriteToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
          <div class="toast-body">
            Status favorit diperbarui!
          </div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
      </div>
      <div id="deleteToast" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
          <div class="toast-body">
            Menu berhasil dihapus!
          </div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
      </div>
    </div>
    
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Axios untuk AJAX -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <!-- SweetAlert2 JS untuk dialog interaktif -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Fungsi toggle sidebar dan update interaktivitas menu
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('content');
            sidebar.classList.toggle('closed');
            
            // Jika sidebar tertutup, tambahkan class "expanded" pada konten
            if (sidebar.classList.contains('closed')) {
                content.classList.add('expanded');
            } else {
                content.classList.remove('expanded');
            }
            
            // Update ikon tombol toggle
            const toggleBtn = document.querySelector('.toggle-btn');
            toggleBtn.innerHTML = sidebar.classList.contains('closed') ? '☰' : '✖';
        }
        
        // Inisialisasi tooltips Bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
          return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Fungsi debounce untuk pencarian yang lebih responsif
        function debounce(func, delay) {
            let debounceTimer;
            return function() {
                const context = this;
                const args = arguments;
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => func.apply(context, args), delay);
            };
        }
        
        // Filter menu berdasarkan input pencarian dengan debounce
        document.getElementById('menuSearch').addEventListener('keyup', debounce(function() {
            let filter = this.value.toLowerCase();
            let menuItems = document.querySelectorAll('.menu-item');
            menuItems.forEach(function(item) {
                let title = item.querySelector('.card-title').textContent.toLowerCase();
                item.style.display = title.indexOf(filter) > -1 ? '' : 'none';
            });
        }, 300));
        
        // Sorting menu dengan animasi
        document.getElementById('sortMenu').addEventListener('change', function() {
            let sortValue = this.value;
            let container = document.getElementById('menuContainer');
            let items = Array.from(container.querySelectorAll('.menu-item'));

            if (sortValue === 'name-asc') {
                items.sort((a, b) => a.dataset.name.localeCompare(b.dataset.name));
            } else if (sortValue === 'price-asc') {
                items.sort((a, b) => parseFloat(a.dataset.price) - parseFloat(b.dataset.price));
            } else if (sortValue === 'price-desc') {
                items.sort((a, b) => parseFloat(b.dataset.price) - parseFloat(a.dataset.price));
            } else {
                // Default: urutan semula, misalnya berdasarkan harga menurun
                items.sort((a, b) => b.dataset.price - a.dataset.price);
            }
            container.innerHTML = '';
            items.forEach(item => {
                item.classList.add('animate__fadeInUp');
                container.appendChild(item);
            });
        });
        
        // Toggle favorite button dengan animasi bounce
        function toggleFavorite(btn) {
            let favorited = btn.getAttribute('data-favorited') === 'true';
            btn.classList.remove('animate__animated', 'animate__bounce');
            if (favorited) {
                btn.setAttribute('data-favorited', 'false');
                btn.innerHTML = '<i class="far fa-heart"></i>';
                btn.classList.remove('favorited');
            } else {
                btn.setAttribute('data-favorited', 'true');
                btn.innerHTML = '<i class="fas fa-heart"></i>';
                btn.classList.add('favorited');
            }
            btn.classList.add('animate__animated', 'animate__bounce');
            setTimeout(() => {
                btn.classList.remove('animate__animated', 'animate__bounce');
            }, 1000);
            showFavoriteToast();
        }
        
        // Fungsi untuk menampilkan toast notifikasi favorit
        function showFavoriteToast() {
            var toastEl = document.getElementById('favoriteToast');
            var toast = new bootstrap.Toast(toastEl);
            toast.show();
        }
        
        // Konfirmasi hapus menggunakan SweetAlert2
        function confirmDelete(id, gambar) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Apakah anda yakin ingin menghapus menu ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.post('entri_referensi.php', new URLSearchParams({
                        hapus_menu: id,
                        gambar_menu: gambar
                    })).then(function(response) {
                        let card = document.querySelector(`[onclick="confirmDelete(${id}, '${gambar}')"]`).closest('.menu-item');
                        card.classList.add('animate__fadeOut');
                        setTimeout(() => { card.remove(); showDeleteToast(); }, 500);
                    }).catch(function(error) {
                        console.error(error);
                    });
                }
            });
        }
    </script>
    
    <?php
    // Proses penghapusan data jika request POST (fallback jika JavaScript tidak aktif)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus_menu'])) {
        $id_masakan = $_POST['hapus_menu'];
        $gambar = $_POST['gambar_menu'];
        $query_lihat = "SELECT * FROM masakan WHERE id_masakan = $id_masakan";
        $sql_lihat = mysqli_query($conn, $query_lihat);
        $result_lihat = mysqli_fetch_array($sql_lihat);
        if ($gambar && file_exists('gambar/' . $gambar)) {
            unlink('gambar/' . $gambar);
        }
        $query_hapus_masakan = "DELETE FROM masakan WHERE id_masakan = $id_masakan";
        mysqli_query($conn, $query_hapus_masakan);
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            echo "success";
            exit();
        }
        header('Location: entri_referensi.php');
        exit();
    }
    ?>
</body>
</html>
<?php
    }
} else {
    header('Location: logout.php');
}
ob_flush();
?>
