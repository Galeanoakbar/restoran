<!DOCTYPE html>
<html lang="en">
<?php
include "connection/koneksi.php";
session_start();
ob_start();

$id = $_SESSION['id_user'];

if(isset($_SESSION['edit_order'])){
  unset($_SESSION['edit_order']);
}

if(isset ($_SESSION['username'])){
  
  $query = "select * from user natural join level where id_user = $id";
  mysqli_query($conn, $query);
  $sql = mysqli_query($conn, $query);

  while($r = mysqli_fetch_array($sql)){
    
    $nama_user = $r['nama_user'];
?>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="Restaurant Invoice">
  <meta name="author" content="Creative Tim">
  <title>Invoice</title>
  
  <!-- Add Bootstrap & FontAwesome -->
  <link rel="stylesheet" href="template/dashboard/css/bootstrap.min.css" />
  <link rel="stylesheet" href="template/dashboard/css/font-awesome.css" />
  <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
  
  <style>
    body {
      font-family: 'Open Sans', sans-serif;
      background-color: #f7f7f7;
      margin: 0;
      padding: 0;
    }
    .container {
      max-width: 900px;
      margin: 30px auto;
      padding: 20px;
      background-color: #fff;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      border-radius: 8px;
    }
    h4, h5 {
      font-size: 24px;
      color: #333;
      font-weight: bold;
      margin-bottom: 10px;
    }
    .invoice-header {
      text-align: center;
      margin-bottom: 20px;
    }
    .invoice-header h4 {
      font-size: 28px;
      color: #1e7e34;
    }
    .invoice-header span {
      font-size: 14px;
      color: #777;
    }
    .table th, .table td {
      padding: 12px;
      text-align: left;
      font-size: 16px;
      border: 1px solid #ddd;
    }
    .table th {
      background-color: #f8f9fa;
      font-weight: bold;
    }
    .table td {
      background-color: #fff;
    }
    .btn-print {
      background-color: #28a745;
      color: white;
      padding: 12px 24px;
      font-size: 18px;
      border-radius: 4px;
      display: inline-block;
      margin-top: 20px;
      transition: background-color 0.3s ease;
    }
    .btn-print:hover {
      background-color: #218838;
    }
    .footer {
      text-align: center;
      margin-top: 30px;
      font-size: 16px;
      color: #777;
    }
    .total-row {
      font-weight: bold;
    }
    /* Interactive hover effect */
    .table tbody tr:hover {
      background-color: #f1f1f1;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }
    @media print {
      body, page {
        margin: 0;
        box-shadow: none;
      }
      .btn-print {
        display: none;
      }
    }
  </style>
</head>

<body>
  <span id="remove">
      <a class="btn-print" id="ct"><i class="fa fa-print"></i> Print Invoice</a>
    </span>
  <div class="container">
    <div class="invoice-header">
      <h4>Restaurant Cepat Saji</h4>
      <br>
      <span>Jl. Taman Safari II, Gn. Princi, Jatiarjo, Kec. Prigen, Pasuruan, Jawa Timur  </span>
      <br>
      <span>Telp. +6289 xxx xxx xxx</span>
    </div>
    <br>
    <?php
      $id_order = $_REQUEST['konten'];
      $query_order = "select * from order_pesanan left join user on order_pesanan.id_pengunjung = user.id_user where id_order = $id_order";
      $sql_order = mysqli_query($conn, $query_order);
      $result_order = mysqli_fetch_array($sql_order);
    ?>
    
    <table class="table">
      <tr>
        <td><strong>Nama Pelanggan</strong></td>
        <td>:</td>
        <td><?php echo $result_order['nama_user'];?></td>
      </tr>
      <tr>
        <td><strong>Nama Kasir</strong></td>
        <td>:</td>
        <td><?php echo $nama_user;?></td>
      </tr>
      <tr>
        <td><strong>Waktu Pesan</strong></td>
        <td>:</td>
        <td><?php echo $result_order['waktu_pesan'];?></td>
      </tr>
      <tr>
        <td><strong>No Meja</strong></td>
        <td>:</td>
        <td><?php echo $result_order['no_meja'];?></td>
      </tr>
    </table>

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
          $query_order_fiks = "select * from tb_pesan natural join masakan where id_order = $id_order";
          $sql_order_fiks = mysqli_query($conn, $query_order_fiks);
          while($r_order_fiks = mysqli_fetch_array($sql_order_fiks)){
        ?>
        <tr>
          <td class="text-center"><?php echo $no_order_fiks++; ?>.</td>
          <td><?php echo $r_order_fiks['nama_masakan'];?></td>
          <td class="text-right"><?php echo $r_order_fiks['jumlah'];?></td>
          <td class="text-right">Rp. <?php echo $r_order_fiks['harga'];?>,-</td>
          <td class="text-right"><strong>Rp. <?php echo $r_order_fiks['harga'] * $r_order_fiks['jumlah'];?>,-</strong></td>
        </tr>
        <?php
          }
          $query_harga = "select * from order_pesanan where id_order = $id_order";
          $sql_harga = mysqli_query($conn, $query_harga);
          $result_harga = mysqli_fetch_array($sql_harga);
        ?>
        <tr class="total-row">
          <td colspan="4" class="text-right">Total</td>
          <td class="text-right"><strong>Rp. <?php echo $result_harga['total_harga'];?>,-</strong></td>
        </tr>
        <tr class="total-row">
          <td colspan="4" class="text-right">Uang Bayar</td>
          <td class="text-right"><strong>Rp. <?php echo $result_harga['uang_bayar'];?>,-</strong></td>
        </tr>
        <tr class="total-row">
          <td colspan="4" class="text-right">Uang Kembali</td>
          <td class="text-right"><strong>Rp. <?php echo $result_harga['uang_kembali'];?>,-</strong></td>
        </tr>
      </tbody>
    </table>
  <br>
    <div class="footer">
      <h5>TERIMAKASIH ATAS KUNJUNGANNYA</h5>
    </div>
  </div>

  <script type="text/javascript">
    document.getElementById('ct').onclick = function(){
      $("#remove").remove();
      window.print();
    }
  </script>

  <script src="template/dashboard/js/jquery.min.js"></script>
  <script src="template/dashboard/js/bootstrap.min.js"></script>

</body>

<?php
    }
  }
?>

<script type="text/javascript">
  document.getElementById('ct').onclick = function(){
    $("#remove").remove();
    window.print();
  }
</script>

<script src="template/dashboard/js/jquery.min.js"></script>
<script src="template/dashboard/js/bootstrap.min.js"></script>
</html>
