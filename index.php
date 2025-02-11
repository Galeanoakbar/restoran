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
    <meta charset="UTF-8">
    <title>Login - Minimalist</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600&display=swap" rel="stylesheet">
    <!-- jQuery UI CSS untuk fitur draggable -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
            height: 100vh;
            margin: 0;
            position: relative; /* Menghindari penggunaan flexbox agar draggable tidak terganggu */
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .login-container {
            position: relative;
            z-index: 2;
            background: rgba(255, 255, 255, 0.85);
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            padding: 40px 30px;
            width: 100%;
            max-width: 400px;
            animation: slideIn 1s ease-out, moveForm 4s ease-in-out infinite;
            }
            @keyframes slideIn {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
            }
            @keyframes moveForm {
            0% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0); }
            }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
            font-size: 28px;
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
            border-color: #1e90ff;
            box-shadow: 0 0 8px rgba(106, 17, 203, 0.3);
            outline: none;
        }
        .btn-login {
            background: #1e90ff;
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
            color:#1e90ff;
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
            color: #1e90ff;
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
        /* Container untuk partikel */
        #particles-js {
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        }
    </style>
</head>
<body>

<div id="particles-js"></div>

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
<!-- jQuery, jQuery UI, dan Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    // Membuat login container dapat dipindahkan (draggable)
    $(function() {
        $(".login-container").draggable();
    });
</script>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <!-- particles.js library -->
  <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
  <script>
    /* Konfigurasi efek partikel */
    particlesJS("particles-js", {
      "particles": {
        "number": { "value": 80, "density": { "enable": true, "value_area": 800 } },
        "color": { "value": "#ffffff" },
        "shape": { "type": "circle", "stroke": { "width": 0, "color": "#000000" } },
        "opacity": { "value": 0.5, "random": false },
        "size": { "value": 3, "random": true },
        "line_linked": { "enable": true, "distance": 150, "color": "#ffffff", "opacity": 0.4, "width": 1 },
        "move": { "enable": true, "speed": 6, "direction": "none", "random": false, "straight": false, "out_mode": "out", "bounce": false }
      },
      "interactivity": {
        "detect_on": "canvas",
        "events": {
          "onhover": { "enable": true, "mode": "repulse" },
          "onclick": { "enable": true, "mode": "push" },
          "resize": true
        },
        "modes": {
          "grab": { "distance": 400, "line_linked": { "opacity": 1 } },
          "bubble": { "distance": 400, "size": 40, "duration": 2, "opacity": 8, "speed": 3 },
          "repulse": { "distance": 200, "duration": 0.4 },
          "push": { "particles_nb": 4 },
          "remove": { "particles_nb": 2 }
        }
      },
      "retina_detect": true
    });
  </script>
</body>
</html>
<?php ob_end_flush(); ?>
