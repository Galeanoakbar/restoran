<!DOCTYPE html>
<?php
include "connection/koneksi.php";
ob_start();
session_start();
?>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sign Up</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
  <style>
    /* Global Background & Font */
    body {
      background: linear-gradient(135deg,rgb(112, 63, 248),rgb(5, 5, 5));
      background-size: cover;
      font-family: 'Roboto', sans-serif;
      height: 100vh;
      margin: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
    }
    /* Dark overlay untuk mempertegas form */
    body::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.6);
      z-index: 1;
    }
    /* Container utama dengan efek glassmorphism */
    .registration-container {
      position: relative;
      z-index: 2;
      background: rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(10px);
      border-radius: 20px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.37);
      display: flex;
      max-width: 900px;
      width: 100%;
      overflow: hidden;
      animation: fadeIn 1s ease-in-out;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: scale(0.9); }
      to { opacity: 1; transform: scale(1); }
    }
    /* Info Section di sebelah kiri */
    .info-section {
      flex: 1;
      background: linear-gradient(135deg, rgba(106,17,203,0.8), rgba(37,117,252,0.8));
      color: #fff;
      padding: 40px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
    }
    .info-section h2 {
      font-size: 2rem;
      margin-bottom: 1rem;
    }
    .info-section p {
      font-size: 1.1rem;
    }
    /* Form Section di sebelah kanan */
    .form-section {
      flex: 1;
      padding: 40px;
      background: rgba(255, 255, 255, 0.9);
    }
    .form-section h2 {
      text-align: center;
      margin-bottom: 30px;
      font-weight: 700;
      color: #333;
    }
    .form-control {
      border-radius: 50px;
      padding: 15px 20px;
      border: none;
      margin-bottom: 20px;
      background-color: #f1f1f1;
      transition: background-color 0.3s, box-shadow 0.3s;
    }
    .form-control:focus {
      background-color: #fff;
      box-shadow: 0 0 10px rgba(106,17,203,0.4);
      outline: none;
    }
    .btn-custom {
      border: none;
      border-radius: 50px;
      padding: 15px 20px;
      width: 100%;
      background: linear-gradient(135deg, #6a11cb, #2575fc);
      color: #fff;
      font-weight: bold;
      transition: transform 0.3s, box-shadow 0.3s;
    }
    .btn-custom:hover {
      transform: scale(1.02);
      box-shadow: 0 5px 15px rgba(37,117,252,0.4);
    }
    .form-footer {
      text-align: center;
      margin-top: 15px;
      font-size: 0.9rem;
    }
    .form-footer a {
      color: #6a11cb;
      text-decoration: none;
    }
    .form-footer a:hover {
      text-decoration: underline;
    }
    @media (max-width: 768px) {
      .registration-container {
        flex-direction: column;
      }
      .info-section, .form-section {
        padding: 20px;
      }
      .info-section {
        display: none; /* Sembunyikan info section di layar kecil */
      }
    }
  </style>
</head>
<body>
<div class="registration-container">
  <!-- Info Section -->
  <div class="info-section d-none d-md-flex">
    <h2>Welcome Aboard!</h2>
    <p>Join us and start your amazing journey. Sign up now and be part of our vibrant community.</p>
  </div>
  <!-- Form Section -->
  <div class="form-section">
    <h2>Create Account</h2>
    <form action="" method="post">
      <input name="nama_user" type="text" class="form-control" placeholder="Full Name" required />
      <input name="username" type="text" class="form-control" placeholder="Username" required />
      <input name="password" type="password" class="form-control" placeholder="Password" required />
      <select name="id_level" class="form-control" required>
        <option value="" disabled selected>Select Your Role</option>
        <option value="1">Administrator</option>
        <option value="2">Waiter</option>
        <option value="3">Kasir</option>
        <option value="4">Owner</option>
        <option value="5">Pelanggan</option>
      </select>
      <button type="submit" name="kirim_daftar" class="btn btn-custom">Create Account</button>
      <?php
      if (isset($_POST['kirim_daftar'])) {
          $nama_user = mysqli_real_escape_string($conn, $_POST['nama_user']);
          $username = mysqli_real_escape_string($conn, $_POST['username']);
          $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
          $id_level = mysqli_real_escape_string($conn, $_POST['id_level']);
          $status = 'nonaktif';

          $query_daftar = "INSERT INTO user (username, password, nama_user, id_level, status) VALUES ('$username', '$password', '$nama_user', '$id_level', '$status')";
          $sql_daftar = mysqli_query($conn, $query_daftar);

          if ($sql_daftar) {
              $_SESSION['daftar'] = 'sukses';
              header('location: index.php');
              exit();
          } else {
              echo "<div class='alert alert-danger mt-3'>Registration failed. Please try again.</div>";
          }
      }
      ?>
    </form>
    <div class="form-footer">
      Already have an account? <a href="index.php">Sign In</a>
    </div>
  </div>
</div>
<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php 
ob_flush();
?>
