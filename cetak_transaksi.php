<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Invoice - Restaurant Cepat Saji</title>
  <!-- Bootstrap CSS & FontAwesome -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Open Sans', sans-serif;
      background: #f0f2f5;
      margin: 0;
      padding: 0;
    }
    .invoice-container {
      max-width: 900px;
      margin: 30px auto;
      background: #fff;
      padding: 20px 30px;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .invoice-header {
      text-align: center;
      border-bottom: 2px solid #28a745;
      padding-bottom: 15px;
      margin-bottom: 20px;
    }
    .invoice-header h2 {
      color: #28a745;
      margin-bottom: 5px;
    }
    .invoice-header p {
      font-size: 14px;
      color: #6c757d;
      margin: 0;
    }
    .invoice-info td {
      padding: 8px 10px;
    }
    .table thead th {
      background-color: #f8f9fa;
      border-bottom: 2px solid #dee2e6;
    }
    .table tbody tr:hover {
      background-color: #f1f1f1;
      transition: background-color 0.3s;
    }
    .total-row {
      font-weight: bold;
      font-size: 18px;
    }
    .btn-print {
      background-color: #28a745;
      color: #fff;
      border: none;
      padding: 10px 20px;
      font-size: 16px;
      border-radius: 5px;
      margin-bottom: 20px;
    }
    .btn-print:hover {
      background-color: #218838;
    }
    @media print {
      .btn-print { display: none; }
      body { background: #fff; }
      .invoice-container { box-shadow: none; }
    }
  </style>
</head>
<body>
<?php
include "connection/koneksi.php";
session_start();
ob_start();

$id = $_SESSION['id_user'];

if(isset($_SESSION['edit_order'])){
  unset($_SESSION['edit_order']);
}

if(isset($_SESSION['username'])){
  $query = "SELECT * FROM user NATURAL JOIN level WHERE id_user = $id";
  $sql = mysqli_query($conn, $query);
  while($r = mysqli_fetch_array($sql)){
    $nama_user = $r['nama_user'];
?>
<div class="container invoice-container">
  <div class="text-right">
    <button class="btn btn-print" id="btnPrint"><i class="fas fa-print"></i> Print Invoice</button>
  </div>
  <div class="invoice-header">
    <h2>Restaurant Cepat Saji</h2>
    <p>Jl. Taman Safari II, Gn. Princi, Jatiarjo, Kec. Prigen, Pasuruan, Jawa Timur</p>
    <p>Telp. +6289 xxx xxx xxx</p>
  </div>

  <?php
    $id_order = $_REQUEST['konten'];
    $query_order = "SELECT * FROM order_pesanan LEFT JOIN user ON order_pesanan.id_pengunjung = user.id_user WHERE id_order = $id_order";
    $sql_order = mysqli_query($conn, $query_order);
    $result_order = mysqli_fetch_array($sql_order);
  ?>
  <table class="table invoice-info">
    <tr>
      <td><strong>Nama Pelanggan:</strong></td>
      <td><?php echo $result_order['nama_user']; ?></td>
      <td><strong>No. Meja:</strong></td>
      <td><?php echo $result_order['no_meja']; ?></td>
    </tr>
    <tr>
      <td><strong>Nama Kasir:</strong></td>
      <td><?php echo $nama_user; ?></td>
      <td><strong>Waktu Pesan:</strong></td>
      <td><?php echo $result_order['waktu_pesan']; ?></td>
    </tr>
  </table>

  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th>No.</th>
          <th>Menu</th>
          <th class="text-right">Jumlah</th>
          <th class="text-right">Harga</th>
          <th class="text-right">Total</th>
        </tr>
      </thead>
      <tbody>
        <?php
          $no_order_fiks = 1;
          $query_order_fiks = "SELECT * FROM tb_pesan NATURAL JOIN masakan WHERE id_order = $id_order";
          $sql_order_fiks = mysqli_query($conn, $query_order_fiks);
          while($r_order_fiks = mysqli_fetch_array($sql_order_fiks)){
        ?>
        <tr>
          <td class="text-center"><?php echo $no_order_fiks++; ?>.</td>
          <td><?php echo $r_order_fiks['nama_masakan']; ?></td>
          <td class="text-right"><?php echo $r_order_fiks['jumlah']; ?></td>
          <td class="text-right">Rp. <?php echo number_format($r_order_fiks['harga'], 0, ',', '.'); ?>,-</td>
          <td class="text-right"><strong>Rp. <?php echo number_format($r_order_fiks['harga'] * $r_order_fiks['jumlah'], 0, ',', '.'); ?>,-</strong></td>
        </tr>
        <?php } ?>
        <?php
          $query_harga = "SELECT * FROM order_pesanan WHERE id_order = $id_order";
          $sql_harga = mysqli_query($conn, $query_harga);
          $result_harga = mysqli_fetch_array($sql_harga);
        ?>
        <tr class="total-row">
          <td colspan="4" class="text-right">Total</td>
          <td class="text-right">Rp. <?php echo number_format($result_harga['total_harga'], 0, ',', '.'); ?>,-</td>
        </tr>
        <tr class="total-row">
          <td colspan="4" class="text-right">Uang Bayar</td>
          <td class="text-right">Rp. <?php echo number_format($result_harga['uang_bayar'], 0, ',', '.'); ?>,-</td>
        </tr>
        <tr class="total-row">
          <td colspan="4" class="text-right">Uang Kembali</td>
          <td class="text-right">Rp. <?php echo number_format($result_harga['uang_kembali'], 0, ',', '.'); ?>,-</td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="text-center mt-4">
    <h4>TERIMAKASIH ATAS KUNJUNGANNYA</h4>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script>
  $('#btnPrint').on('click', function(){
    $('.btn-print').hide();
    window.print();
    $('.btn-print').show();
  });
</script>
<?php
  }
}
?>
</body>
</html>
