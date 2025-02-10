<?php
ob_start();
include "connection/koneksi.php";
session_start();

// Redirect ke halaman beranda jika sudah login
if (isset($_SESSION['username'])) {
    header('location: beranda.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login - Minimalist</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS untuk layout dasar -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome untuk ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <!-- Google Fonts untuk tipografi modern -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f7f7f7;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            width: 360px;
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
            font-size: 24px;
            font-weight: 600;
            color: #333;
        }
        .form-control {
            height: 45px;
            border: 1px solid #ddd;
            border-radius: 8px;
            transition: border-color 0.3s, box-shadow 0.3s;
            font-size: 16px;
            margin-bottom: 20px;
        }
        .form-control:focus {
            border-color: #6A11CB;
            box-shadow: 0 0 8px rgba(106, 17, 203, 0.3);
            outline: none;
        }
        .btn-login {
            background: #6A11CB;
            border: none;
            border-radius: 8px;
            padding: 12px;
            color: #fff;
            font-size: 16px;
            font-weight: 500;
            width: 100%;
            transition: background 0.3s;
        }
        .btn-login:hover {
            background: #2575FC;
        }
        .social-icons {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }
        .social-icons a {
            color: #6A11CB;
            font-size: 20px;
            margin: 0 10px;
            transition: color 0.3s;
        }
        .social-icons a:hover {
            color: #2575FC;
        }
        .create-account {
            text-align: center;
            font-size: 14px;
        }
        .create-account a {
            color: #6A11CB;
            text-decoration: none;
            font-weight: 500;
        }
        .create-account a:hover {
            text-decoration: underline;
        }
        .alert-danger {
            background: #ffe5e5;
            color: #d9534f;
            padding: 10px;
            border-radius: 6px;
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="login-container">
    <div class="login-header">Login</div>
    <form action="" method="post">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert-danger">
                <?= $_SESSION['error'] ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        <input type="text" name="username" class="form-control" placeholder="Username" required autofocus>
        <input type="password" name="password" class="form-control" placeholder="Password" required>
        <button type="submit" name="login" class="btn btn-login">Login</button>
        <div class="social-icons">
            <a href="#" title="Login with Facebook"><i class="fab fa-facebook"></i></a>
            <a href="#" title="Login with Google"><i class="fab fa-google"></i></a>
            <a href="#" title="Login with Twitter"><i class="fab fa-twitter"></i></a>
        </div>
        <div class="create-account">
            <a href="daftar.php">Create new account</a>
        </div>
    </form>
</div>
<!-- jQuery dan Bootstrap JS untuk interaktivitas dasar -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<?php
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query untuk mencari pengguna
    $query = "SELECT * FROM user NATURAL JOIN level WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        // Validasi password dan status akun
        if (password_verify($password, $user['password']) && $user['status'] === 'aktif') {
            $_SESSION['username'] = $username;
            $_SESSION['id_user'] = $user['id_user'];
            $_SESSION['level'] = $user['id_level'];

            // Redirect berdasarkan level pengguna
            switch ($user['id_level']) {
                case 1:
                    header('location: beranda.php');
                    break;
                case 2:
                    header('location: entri_order.php');
                    break;
                case 3:
                    header('location: entri_transaksi.php');
                    break;
                case 4:
                    header('location: generate_laporan.php');
                    break;
                case 5:
                    header('location: entri_order.php');
                    break;
                default:
                    $_SESSION['error'] = 'Level pengguna tidak valid.';
                    header('location: index.php');
                    break;
            }
            exit();
        } else {
            $_SESSION['error'] = 'Password salah atau akun belum divalidasi.';
        }
    } else {
        $_SESSION['error'] = 'Username tidak ditemukan.';
    }

    header('location: index.php');
    exit();
}
?>

</body>
</html>
<?php ob_end_flush(); ?>
