<?php

include "connection.php";

$query = mysqli_query($connection, "SELECT * FROM destination");

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Destinations</title>
  <link rel="stylesheet" href="styles/destination.css" />
</head>

<body>
  <div class="navbar">
    <div class="navbar_container">
      <div class="brand">
        <img src="images/logo.png" alt="logo" />
      </div>
      <div class="menu">
        <a class="nav-item" href="index.php">Home</a>
        <a class="nav-item active" href="#">Destination</a>
        <a class="nav-item" href="about.php">About</a>
        <a class="nav-item" href="contact.php">Contact</a>
        <a class="nav-item" href="visa.php">Visa</a>
        <a class="nav-item" href="transport.php">Transport</a>
        <a class="nav-item" href="tiket.php">Tiket</a>
      </div>
    </div>
  </div>

  <div class="wrapper">
    <h1 class="page-title">Explore Our Destinations</h1>
    <div class="destination">
      <?php
      while ($data = mysqli_fetch_assoc($query)) {
      ?>
        <div class="card_destination">
          <a href="detail.php?id=<?= $data['id_des'] ?>">
            <img src="images/<?= $data['gambar']; ?>" alt="<?= $data['nama_des'] ?>" />
            <div>
              <h2><?= $data["nama_des"] ?></h2>
            </div>
          </a>
        </div>
      <?php } ?>
    </div>
  </div>
</body>

</html>
