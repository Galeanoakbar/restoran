<?php
ob_start();
include "connection/koneksi.php";
session_start();

// Redirect ke halaman sesuai level jika sudah login
if (isset($_SESSION['username'])) {
    header('location: beranda.php');
    exit();
}

// Proses login
if (isset($_POST['login_submit'])) {
    $username = $_POST['login_username'];
    $password = $_POST['login_password'];

    $query = "SELECT * FROM user NATURAL JOIN level WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        if (password_verify($password, $user['password']) && $user['status'] === 'aktif') {
            $_SESSION['username'] = $username;
            $_SESSION['id_user'] = $user['id_user'];
            $_SESSION['level'] = $user['id_level'];

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

// Proses pendaftaran
if (isset($_POST['register_submit'])) {
    $nama_user = mysqli_real_escape_string($conn, $_POST['nama_user']);
    $username = mysqli_real_escape_string($conn, $_POST['register_username']);
    $password = password_hash($_POST['register_password'], PASSWORD_BCRYPT);
    $id_level = mysqli_real_escape_string($conn, $_POST['id_level']);
    $status = 'nonaktif';

    $query_daftar = "INSERT INTO user (username, password, nama_user, id_level, status) VALUES ('$username', '$password', '$nama_user', '$id_level', '$status')";
    $sql_daftar = mysqli_query($conn, $query_daftar);

    if ($sql_daftar) {
        $_SESSION['daftar'] = 'sukses';
        header('location: index.php');
        exit();
    } else {
        $_SESSION['error'] = 'Registration failed. Please try again.';
    }
    header('location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login & Registration</title>
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
  <style>
    /* Import font Poppins yang elegan */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
    
    /* Global Reset & Styling */
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: 'Poppins', sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      overflow: hidden;
    }
    /* Container utama dengan sudut membulat */
    .container {
      background: #fff;
      border-radius: 30px;
      box-shadow: 0 14px 28px rgba(0,0,0,0.25), 0 10px 10px rgba(0,0,0,0.22);
      position: relative;
      overflow: hidden;
      width: 768px;
      max-width: 100%;
      min-height: 480px;
      animation: slideIn 1s ease-out, moveForm 4s ease-in-out infinite;
    }
    @keyframes moveForm {
      0% { transform: translateY(0); }
      60% { transform: translateY(-10px); }
      100% { transform: translateY(0); }
    }
    .form-container {
      position: absolute;
      top: 0;
      height: 100%;
      transition: all 0.6s ease-in-out;
    }
    /* Container form Sign In */
    .sign-in-container {
      left: 0;
      width: 50%;
      z-index: 2;
      padding: 0 50px;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    /* Container form Sign Up */
    .sign-up-container {
      left: 0;
      width: 50%;
      opacity: 0;
      z-index: 1;
      padding: 0 50px;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    /* Tampilkan Sign Up saat container aktif */
    .container.active .sign-up-container {
      transform: translateX(100%);
      opacity: 1;
      z-index: 5;
      animation: show 0.6s;
    }
    .container.active .sign-in-container {
      transform: translateX(100%);
    }
    @keyframes show {
      0%, 49.99% { opacity: 0; z-index: 1; }
      50%, 100% { opacity: 1; z-index: 5; }
    }
    form {
      background: #fff;
      display: flex;
      flex-direction: column;
      padding: 0 50px;
      height: 100%;
      justify-content: center;
      align-items: center;
      text-align: center;
    }
    h2 {
      margin-bottom: 20px;
      font-weight: 600;
      color: #333;
      white-space: nowrap;
      font-size: 24px; /* Sesuaikan ukuran font */
      display: inline-block; /* Pastikan tetap dalam satu baris */
      text-align: center;
    }
    input, select {
      background: #eee;
      border: none;
      padding: 12px 15px;
      margin: 8px 0;
      width: 120%;
      max-width: 400px;
      border-radius: 20px;
      transition: box-shadow 0.3s ease;
    }
    input:focus, select:focus {
      outline: none;
      box-shadow: 0 0 10px rgba(81, 45, 168, 0.5);
    }
    button {
      border-radius: 10px;
      border: 1px solid #512da8;
      background: #512da8;
      color: #fff;
      font-size: 12px;
      padding: 12px 45px;
      margin-top: 10px;
      letter-spacing: 1px;
      text-transform: uppercase;
      transition: transform 80ms ease-in, background 0.3s ease;
      cursor: pointer;
    }
    button:hover {
      background: #5c6bc0;
    }
    button:active { transform: scale(0.95); }
    button:focus { outline: none; }
    .social-container {
      margin: 20px 0;
    }
    .social-container a {
      border: 1px solid #fff;
      border-radius: 50%;
      display: inline-flex;
      justify-content: center;
      align-items: center;
      margin: 0 5px;
      height: 40px;
      width: 40px;
      color: #fff;
      background: #512da8;
      text-decoration: none;
      transition: transform 0.3s ease;
    }
    .social-container a:hover {
      transform: scale(1.1);
    }
    .alert { color: #ff0000; margin-bottom: 10px; }
    
    /* Overlay Panel dengan efek animasi gradient */
    .overlay-container {
      position: absolute;
      top: 0;
      left: 50%;
      width: 50%;
      height: 100%;
      overflow: hidden;
      transition: transform 0.6s ease-in-out;
      z-index: 100;
      border-top-right-radius: 30px;
      border-bottom-right-radius: 30px;
      border-radius: 30px;
    }
    .container.active .overlay-container {
      transform: translateX(-100%);
    }
    .overlay {
      background: #553dae;
      background-size: 400% 400%;
      animation: gradientAnimation 15s ease infinite;
      color: #fff;
      position: relative;
      left: -100%;
      height: 100%;
      width: 200%;
      transform: translateX(0);
      transition: transform 0.6s ease-in-out;
    }
    @keyframes gradientAnimation {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }
    .container.active .overlay {
      transform: translateX(50%);
    }
    .overlay-panel {
      position: absolute;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
      padding: 0 40px;
      top: 0;
      height: 100%;
      width: 50%;
      transition: transform 0.6s ease-in-out;
    }
    .overlay-left { 
      transform: translateX(-20%);
      border-top-left-radius: 30px;
      border-bottom-left-radius: 30px;
    }
    .container.active .overlay-left { transform: translateX(0); }
    .overlay-right { 
      right: 0; 
      transform: translateX(0);
      border-top-right-radius: 30px;
      border-bottom-right-radius: 30px;
    }
    .container.active .overlay-right { transform: translateX(20%); }
    .ghost {
      background: transparent;
      border-color: #fff;
      color: #fff;
      border-radius: 10px;
      padding: 12px 45px;
      margin-top: 10px;
      letter-spacing: 1px;
      text-transform: uppercase;
      cursor: pointer;
      transition: background 0.3s ease;
    }
    .ghost:hover {
      background: rgba(255, 255, 255, 0.2);
    }
    .forgot-password {
    color: #512da8;
    text-decoration: none;
    font-size: 14px;
    margin-top: 10px;
    display: inline-block;
    transition: color 0.3s ease-in-out;
    }

    .forgot-password:hover {
    color: #5c6bc0;
    text-decoration: underline;
    }

  </style>
</head>
<body>
  <div class="container" id="container">
    <!-- Form Login (Sign In) -->
    <div class="form-container sign-in-container">
      <form action="" method="post">
        <h2>Sign In</h2>
        <?php if(isset($_SESSION['error'])): ?>
          <div class="alert"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <input type="text" name="login_username" placeholder="Username" required autofocus />
        <input type="password" name="login_password" placeholder="Password" required />
        <a href="forgot_password.php" class="forgot-password">Forgot Your Password?</a>
        <button type="submit" name="login_submit">Sign In</button>

      </form>
    </div>
    <!-- Form Pendaftaran (Sign Up) -->
    <div class="form-container sign-up-container">
      <form action="" method="post">
        <h2>Create Account</h2>
        <input type="text" name="nama_user" placeholder="Full Name" required />
        <input type="text" name="register_username" placeholder="Username" required />
        <input type="password" name="register_password" placeholder="Password" required />
        <select name="id_level" required>
          <option value="" disabled selected>Select Your Role</option>
          <option value="1">Administrator</option>
          <option value="2">Waiter</option>
          <option value="3">Kasir</option>
          <option value="4">Owner</option>
          <option value="5">Pelanggan</option>
        </select>
        <button type="submit" name="register_submit">Sign Up</button>
      </form>
    </div>
    <!-- Overlay Panel -->
    <div class="overlay-container">
      <div class="overlay">
        <div class="overlay-panel overlay-left">
          <h2>Welcome Back!</h2>
          <p>To keep connected with us, please login with your personal info</p><br>
          <button class="ghost" id="signIn">Sign In</button>
        </div>
        <div class="overlay-panel overlay-right">
          <h2>Hello, Friend!</h2>
          <p>Enter your personal details and start your journey with us</p><br>
          <button class="ghost" id="signUp">Sign Up</button>
        </div>
      </div>
    </div>
  </div>
  <script>
    const container = document.getElementById('container');
    const signUpButton = document.getElementById('signUp');
    const signInButton = document.getElementById('signIn');
    
    signUpButton.addEventListener('click', () => {
      container.classList.add("active");
    });
    
    signInButton.addEventListener('click', () => {
      container.classList.remove("active");
    });
  </script>
</body>
</html>
<?php ob_end_flush(); ?>
