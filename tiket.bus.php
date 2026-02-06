<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8" />
  <link rel="stylesheet" href="styles/tiket.bus.css" />
  <title>Tiket Bus</title>
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
  <div class="tiket">
    <div class="navbar">
      <div class="navbar_container">
        <div class="brand">
          <img src="images/logo.png" alt="logo" />
        </div>
        <div class="menu">
          <a class="nav-item" href="index.php">Home</a>
          <a class="nav-item" href="destination.html">Destination</a>
          <a class="nav-item" href="about.html">About</a>
          <a class="nav-item" href="contact.html">Contact</a>
          <a class="nav-item" href="visa.php">Visa</a>
          <a class="nav-item" href="transport.php">Transport</a>
          <a class="nav-item active" href="#">Tiket</a>
        </div>
      </div>
    </div>

    <div class="main-content">
      <div class="bus-ticket-section">
        <div class="bus-image">
          <img src="images/tiket/bus.png" alt="Bus Image" />
          <div class="back-overlay">
            <a class="back-button" href="tiket.php">Kembali</a>
          </div>
          <div class="header-overlay">
            <h1>Cek Harga Tiket Bus Online Beserta Promo Hari Ini!</h1>
          </div>
          <div class="ticket-form-overlay">
            <form action="hasil.bus.php" method="GET">
              <label for="from">Dari</label>
              <select id="from" name="from">
                <option value="">-- Pilih Kota Asal --</option>
                <option value="1">Surabaya</option>
                <option value="2">Denpasar</option>
              </select>

              <label for="to">Ke</label>
              <select id="to" name="to">
                <option value="">-- Pilih Kota Tujuan --</option>
                <option value="2">Denpasar</option>
                <option value="1">Surabaya</option>
              </select>

              <div class="date-container">
                <label for="departure-date">
                  Pergi
                  <span style="display: inline-flex; align-items: center; margin-left: 110.7px;">
                    <label for="return-switch">Pulang-Pergi?</label>
                    <label class="switch" style="margin-left: 10px;">
                      <input type="checkbox" id="return-switch">
                      <span class="slider"></span>
                    </label>
                  </span>
                </label>
                <input type="date" id="departure-date" name="departure-date" placeholder="Tanggal Keberangkatan">


                <div id="return-date-container" class="hidden">
                  <label for="return-date">Pulang</label>
                  <input type="date" id="return-date" name="return-date" placeholder="Tanggal Kembali (Opsional)">
                </div>
              </div>

              <select id="passengers" name="passengers">
                <option value="">-- Pilih Jumlah Kursi --</option>
                <option value="1">1 Kursi</option>
                <option value="2">2 Kursi</option>
              </select>

              <button type="submit">Cari Tiket</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const departureDate = document.getElementById('departure-date');
      const returnDate = document.getElementById('return-date');
      const returnSwitch = document.getElementById('return-switch');
      const returnDateContainer = document.getElementById('return-date-container');

      departureDate.addEventListener('change', function() {
        if (returnDate.value && new Date(returnDate.value) < new Date(departureDate.value)) {
          alert('Return date cannot be before departure date.');
          returnDate.value = '';
        }
      });

      returnDate.addEventListener('change', function() {
        if (new Date(returnDate.value) < new Date(departureDate.value)) {
          alert('Return date cannot be before departure date.');
          returnDate.value = '';
        }
      });

      returnSwitch.addEventListener('change', function() {
        if (returnSwitch.checked) {
          returnDateContainer.classList.remove('hidden');
        } else {
          returnDateContainer.classList.add('hidden');
          returnDate.value = '';
        }
      });
    });
  </script>
</body>

</html>