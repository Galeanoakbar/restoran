<!DOCTYPE html>
<?php
include "connection/koneksi.php";
ob_start();
session_start();
?>
<html lang="en">
<head>
    <title>Sign Up</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700' rel='stylesheet' type='text/css'>
    <style>
        body {
            background: linear-gradient(135deg, #a1c4fd, #c2e9fb);
            font-family: 'Open Sans', sans-serif;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .registration-container {
            background: white;
            display: flex;
            width: 800px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .form-section {
            width: 50%;
            padding: 30px;
        }
        .info-section {
            width: 50%;
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            color: white;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .form-group input, .form-group select {
            border: 2px solid #cce7ff;
            color: purple;
            border-radius: 50px;
            padding: 15px;
            font-size: 16px;
            width: 100%;
        }
        .form-group input:focus, .form-group select:focus {
            border-color: #6a11cb;
            outline: none;
            box-shadow: 0 0 10px rgba(106, 17, 203, 0.3);
        }
        .btn-custom {
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            color: white;
            border-radius: 50px;
            font-weight: bold;
            padding: 10px 20px;
            transition: background 0.3s;
            width: 100%;
        }
        .btn-custom:hover {
            background: linear-gradient(135deg, #2575fc, #6a11cb);
        }
        .form-footer {
            font-size: 14px;
            margin-top: 15px;
        }
        .form-footer a {
            color: #6a11cb;
            text-decoration: none;
        }
        .form-footer a:hover {
            text-decoration: underline;
        }
        .form-check-label {
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="registration-container">
    <!-- Form Section -->
    <div class="form-section">
        <h2 class="text-center">Let’s Get Started!</h2>
        <br>
        <form action="" method="post">
            <div class="form-group">
                <input name="nama_user" type="text" class="form-control" placeholder="Full Name" required />
            </div>
            <div class="form-group">
                <input name="username" type="text" class="form-control" placeholder="Username" required />
            </div>
            <div class="form-group">
                <input name="password" type="password" class="form-control" placeholder="Password" required />
            </div>
            <div class="form-group">
                <select name="id_level" class="form-control" required>
                    <option value="" disabled selected>Select Your Role</option>
                    <option value='1'>Administrator</option>                    
                    <option value='2'>Waiter</option>
                    <option value='3'>Kasir</option>
                </select>
            </div>
            <button type='submit' name='kirim_daftar' class='btn btn-custom'><i class='icon icon-save'></i>&nbsp; CREATE ACCOUNT</button>
            <?php
            if (isset($_POST['kirim_daftar'])) {
                $nama_user = mysqli_real_escape_string($conn, $_POST['nama_user']);
                $username = mysqli_real_escape_string($conn, $_POST['username']);
                $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash password
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
        <div class="form-footer text-center">
            Already have an account? <a href="index.php">Sign In</a>
        </div>
    </div>

    <!-- Info Section -->
    <div class="info-section">
        <h2>Your Adventure Awaits!</h2>
        <p>Every big journey begins with a single step. Let’s take that step together!</p>
    </div>
</div>

<script src="//code.jquery.com/jquery-3.6.0.min.js"></script> 
<script src="//stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script> 
</body>
</html>

<?php 
ob_flush();
?>
