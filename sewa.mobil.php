<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8" />
  <link rel="stylesheet" href="styles/booking.hotel.css" />
  <title>Sewa Mobil</title>
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

    /* Gaya untuk switch button */
    .switch {
      position: relative;
      display: inline-block;
      width: 30px;
      height: 16px;
    }

    .switch input {
      opacity: 0;
      width: 0;
      height: 0;
    }

    .slider {
      position: absolute;
      cursor: pointer;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: #6750A4;
      transition: 0.4s;
      border-radius: 34px;
    }

    .slider:before {
      position: absolute;
      content: "";
      height: 12px;
      width: 12px;
      left: 3px;
      bottom: 2px;
      background-color: white;
      transition: 0.4s;
      border-radius: 50%;
    }

    input:checked+.slider {
      background-color: #21005D;
    }

    input:checked+.slider:before {
      transform: translateX(14px);
    }

    .date-container .hidden {
      display: none;
    }

    .back-button {
      background-color: #4CAF50;
      color: white;
      text-decoration: none;
      border-radius: 30px;
      padding: 10px 20px;
    }

    .back-button:hover {
      background-color: #45a049;
    }

    h1 {
      font-size: 40px;
    }
  </style>
</head>

<body>
  <div class="booking">
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

    <div class="main-content">
      <div class="hotel-booking-section">
        <div class="hotel-image">
          <img src="images/tiket/rental.jpeg" alt="Hotel Image" />
          <div class="back-overlay">
            <a class="back-button" href="tiket.php">Kembali</a>
          </div>
          <div class="header-overlay">
            <h1>Cek Harga Sewa Mobil Online Beserta Promo Hari Ini!</h1>
          </div>
          <div class="booking-form-overlay">
            <form action="hasil.mobil.php" method="GET">
              <label for="destination">Tujuan</label>
              <select id="destination" name="destination">
                <option value="">-- Pilih Kota Tujuan --</option>
                <option value="1">Surabaya</option>
                <option value="2">Denpasar</option>
              </select>

              <div class="date-container">
                <label for="check-in-date">Tanggal Sewa</label>
                <input type="date" id="check-in-date" name="check-in-date" placeholder="Tanggal Check-In">

                <label for="check-out-date">Tanggal Selesai</label>
                <input type="date" id="check-out-date" name="check-out-date" placeholder="Tanggal Check-Out (Opsional)">

              </div>

              <select id="rooms" name="rooms">
                <option value="">-- Pilih Jumlah Mobil --</option>
                <option value="1">1 Mobil</option>
                <option value="2">2 Mobil</option>
              </select>

              <button type="submit">Cari Mobil</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const checkInDate = document.getElementById('check-in-date');
      const checkOutDate = document.getElementById('check-out-date');
      const returnSwitch = document.getElementById('return-switch');
      const checkOutDateContainer = document.getElementById('check-out-date-container');

      checkInDate.addEventListener('change', function() {
        if (checkOutDate.value && new Date(checkOutDate.value) < new Date(checkInDate.value)) {
          alert('Check-out date cannot be before check-in date.');
          checkOutDate.value = '';
        }
      });

      checkOutDate.addEventListener('change', function() {
        if (new Date(checkOutDate.value) < new Date(checkInDate.value)) {
          alert('Check-out date cannot be before check-in date.');
          checkOutDate.value = '';
        }
      });
    });
  </script>
</body>

</html>