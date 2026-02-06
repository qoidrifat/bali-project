<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Ticket Menu</title>
  <link rel="stylesheet" href="styles/tiket.css" />
</head>

<body>
  <div class="navbar">
    <div class="navbar_container">
      <div class="brand">
        <img src="images/logo.png" alt="logo" />
      </div>
      <div class="menu">
        <a class="nav-item" href="index.php">Home</a>
        <a class="nav-item" href="destination.php">Destination</a>
        <a class="nav-item" href="about.php">About</a>
        <a class="nav-item" href="contact.php">Contact</a>
        <a class="nav-item" href="visa.php">Visa</a>
        <a class="nav-item" href="transport.php">Transport</a>
        <a class="nav-item active" href="#">Tiket</a>
      </div>
    </div>
  </div>

  <div class="tiket-container">
    <div class="page-header">
      <h1>What Are You Looking For Today?</h1>
      <p>Find the best deals on bus tickets, hotels, and car rentals.</p>
    </div>
    <div class="ticket-options">
      <div class="ticket-option">
        <img src="images/tiket/bus_crop.jpeg" alt="Bus Ticket" />
        <div class="option-overlay">
          <div class="title">Bus Tickets</div>
          <a class="lihat-detail" href="tiket.bus.php">View Details</a>
        </div>
      </div>
      <div class="ticket-option">
        <img src="images/tiket/hotel_crop.jpeg" alt="Hotel Booking" />
        <div class="option-overlay">
          <div class="title">Hotel Booking</div>
          <a class="lihat-detail" href="booking.hotel.php">View Details</a>
        </div>
      </div>
      <div class="ticket-option">
        <img src="images/tiket/rental_crop.jpeg" alt="Car Rental" />
        <div class="option-overlay">
          <div class="title">Car Rental</div>
          <a class="lihat-detail" href="sewa.mobil.php">View Details</a>
        </div>
      </div>
    </div>
  </div>

</body>

</html>
