<?php
ob_start(); // Memulai output buffering
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
    <title>Login</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="template/masuk/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="template/masuk/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <style>
        body {
            font-family: "Poppins", sans-serif;
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: radial-gradient(circle, #6A11CB, #2575FC);
            overflow: hidden;
        }
        .container-login100 {
            width: 400px;
            padding: 30px;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        .login-header {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            color: #2575FC;
            margin-bottom: 20px;
        }
        .input100 {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .input100:focus {
            border-color: #2575FC;
            box-shadow: 0 4px 12px rgba(37, 117, 252, 0.5);
            outline: none;
        }
        .login100-form-btn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 10px;
            background: linear-gradient(to right, #6A11CB, #2575FC);
            color: #fff;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .login100-form-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 12px rgba(37, 117, 252, 0.5);
        }
        .link {
            text-align: center;
            margin-top: 20px;
        }
        .link a {
            color: #2575FC;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s ease;
        }
        .link a:hover {
            color: #6A11CB;
        }
        .social-icons {
            display: flex;
            justify-content: center;
            margin-top: 15px;
        }
        .social-icons a {
            margin: 0 10px;
            font-size: 20px;
            color: #2575FC;
            transition: all 0.3s ease;
        }
        .social-icons a:hover {
            color: #6A11CB;
            transform: scale(1.2);
        }
        footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #999;
        }
        .alert-danger {
            background: #ffe5e5;
            color: #d9534f;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="container-login100">
    <div class="login-header">Login</div>
    <form action="" method="post">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert-danger">
                <?= $_SESSION['error'] ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <input class="input100" type="text" name="username" placeholder="Username" required>
        <input class="input100" type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login" class="login100-form-btn">Login</button>

        <div class="social-icons">
            <a href="#" title="Login with Facebook"><i class="fa fa-facebook"></i></a>
            <a href="#" title="Login with Google"><i class="fa fa-google"></i></a>
            <a href="#" title="Login with Twitter"><i class="fa fa-twitter"></i></a>
        </div>

        <div class="link">
            <a href="daftar.php">Create new account</a>
        </div>
    </form>
</div>

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
                case 2:
                    header('location: beranda.php');
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
