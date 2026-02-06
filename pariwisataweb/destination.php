<?php

include "connection.php";

$query = mysqli_query($connection, "SELECT * FROM destination");

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Document</title>
  <link rel="stylesheet" href="styles/destination.css" />
</head>

<body>
  <div class="navbar">
    <div class="navbar_container">
      <div class="brand">
        <img src="images/logo.png" alt="logo" />
      </div>
      <div class="menu">
        <a href="">Home</a>
        <a href="">Destination</a>
        <a href="">About</a>
        <a href="">Contact</a>
        <a href="">Visa</a>
        <a href="">Transport</a>
        <a href="">Tiket</a>
      </div>
    </div>
  </div>
  <div class="wrapper">
    <div class="destination">
      <?php
      while ($data = mysqli_fetch_assoc($query)) {
      ?>
        <div class="card_destination">
          <a href="detail.php?id=<?= $data['id_des'] ?>">
            <img src="images/<?= $data['gambar']; ?>" alt="PANTAI KUTA" />
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