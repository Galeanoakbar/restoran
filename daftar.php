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
    /* Background dark gradient */
    body {
      margin: 0;
      padding: 0;
      background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
      background-size: cover;
      font-family: 'Roboto', sans-serif;
      height: 100vh;
      position: relative;
      overflow: hidden;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    /* Container untuk partikel */
    #particles-js {
      position: absolute;
      width: 100%;
      height: 100%;
      top: 0;
      left: 0;
      z-index: 0;
    }
    /* Container form dengan animasi slide-in dan gerakan lembut */
    .registration-container {
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
    h2 {
      text-align: center;
      color: #333;
      margin-bottom: 20px;
      font-weight: 700;
    }
    .form-control {
      border-radius: 25px;
      padding: 10px 20px;
      border: 1px solid #ddd;
      margin-bottom: 15px;
      transition: box-shadow 0.3s ease;
    }
    .form-control:focus {
      box-shadow: 0 0 8px rgba(30,144,255,0.5);
      outline: none;
    }
    .btn-custom {
      border: none;
      border-radius: 25px;
      padding: 10px;
      width: 100%;
      background-color: #1e90ff;
      color: #fff;
      font-weight: bold;
      transition: background-color 0.3s, transform 0.3s;
    }
    .btn-custom:hover {
      background-color: #187bcd;
      transform: translateY(-2px);
    }
    .btn-custom:disabled {
      background-color: #aaa;
      cursor: not-allowed;
    }
    .form-footer {
      text-align: center;
      margin-top: 10px;
      font-size: 0.9rem;
    }
    .form-footer a {
      color: #1e90ff;
      text-decoration: none;
    }
    .form-footer a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <!-- Container untuk efek partikel -->
  <div id="particles-js"></div>
  
  <!-- Form Sign-Up -->
  <div class="registration-container">
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
  
  <!-- Bootstrap 5 JS Bundle -->
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
<?php 
ob_flush();
?>
