<?php

include "connection.php";

$id = $_GET["id"];
$query = mysqli_query($connection, "SELECT * FROM detail INNER JOIN destination USING(id_des) WHERE id_des = $id");
$data = mysqli_fetch_assoc($query);
$id_detail = $data["id_detail"];
$query_image = mysqli_query($connection, "SELECT gambar FROM detail_image WHERE id_detail = $id_detail");

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Document</title>
  <link rel="stylesheet" href="styles/detail.css" />
  <style>
    .nav-item {
        font-family: "Poppins", sans-serif;
        font-weight: 400;
        font-size: 20px;
        color: #000000;
        cursor: pointer;
        position: relative;
        padding: 5px 0;
      }

      .nav-item::after {
        content: '';
        position: absolute;
        bottom: -5px;
        left: 0;
        width: 0;
        height: 3px;
        background-color: #000000;
        transition: width 0.3s;
      }

      .nav-item:hover {
        font-weight: 700;
      }

      .nav-item:hover::after {
        width: 100%;
      }

      .nav-item.active {
        font-family: "Poppins", sans-serif;
        font-weight: 700;
        position: relative;
      }

      .nav-item.active::after {
        content: '';
        position: absolute;
        bottom: -5px;
        left: 0;
        width: 100%;
        height: 3px;
        background-color: #000000;
      }
  </style>
</head>

<body>
  <div class="navbar">
    <div class="navbar_container">
      <div class="brand">
        <img src="images/logo.png" alt="logo" />
      </div>
      <div class="menu">
        <a class="nav-item" href="index.php">Home</a>
        <a class="nav-item active" href="destination.php">Destination</a>
        <a class="nav-item" href="about.php">About</a>
        <a class="nav-item" href="contact.php">Contact</a>
        <a class="nav-item" href="visa.php">Visa</a>
        <a class="nav-item" href="transport.php">Transport</a>
        <a class="nav-item" href="tiket.php">Tiket</a>
      </div>
    </div>
  </div>
  <div class="wrapper">
    <div class="banner" style="background: linear-gradient(to right, rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0.5)),url('images/<?= $data["gambar"] ?>');background-size: cover; background-position: center">
      <h1><?= $data['nama_des']; ?></h1>
    </div>

    <div class="detail">
      <div class="detail_content">
        <?= $data["desc"]; ?>
      </div>
      <div class="detail_image">
        <?php
        while ($data_image = mysqli_fetch_assoc($query_image)) {
        ?>
          <img src="images/<?= $data_image['gambar']; ?>" alt="thumbnail" />
        <?php } ?>
      </div>
    </div>
  </div>
</body>

</html>