<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Transport Menu</title>
  <link rel="stylesheet" href="styles/transport.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
        <a class="nav-item active" href="#">Transport</a>
        <a class="nav-item" href="tiket.php">Tiket</a>
      </div>
    </div>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <div class="pesawat-ticket-section">
      <img src="images/transport3.png" alt="Pesawat" class="background-plane">
      <div class="header-content">
        <h1>Cek Harga Tiket Pesawat Online Murah dan Promo Hari Ini</h1>
      </div>
      <div class="ticket-form-overlay">
        <form action="hasil.pesawat.php" method="GET">
          <div class="form-main-grid">
            <div class="form-col">
              <div class="form-group" id="from-group">
                <label for="from">Dari</label>
                <div class="input-wrapper">Surabaya SUB</div>
              </div>
              <div class="form-group" id="to-group">
                <label for="to">Ke</label>
                <div class="input-wrapper">Denpasar DPS</div>
              </div>
            </div>
            <div class="swap-icon" id="swap-icon"><i class="fas fa-exchange-alt"></i></div>
            <div class="form-col">
              <div class="form-row">
                <div class="date-group">
                  <label for="departure-date">Pergi</label>
                  <div class="input-wrapper">Senin, 1 April</div>
                </div>
                <div class="round-trip">
                  <span>Pulang-Pergi?</span>
                  <div class="round-trip-toggle" id="round-trip-toggle">
                    <i class="fas fa-check"></i>
                  </div>
                </div>
              </div>
              <div class="form-group" id="return-date-container" style="display: none;">
                <label for="return-date">Pulang</label>
                <div class="input-wrapper">Senin, 8 April</div>
              </div>
            </div>
          </div>
          <div class="passengers-group">
            <span>1 Penumpang, Ekonomi</span>
          </div>
          <button type="submit" class="submit-btn">Ayo Cari</button>
        </form>
      </div>
    </div>

    <div class="recommendations">
      <h2>Rekomendasi Penerbangan Ekslusif</h2>
      <div class="side_by_side_images">
        <div class="card-image1">
          <div class="head">
            <div class="logo"><img src="images/lion.png" alt="Lion Air"></div>
            <div class="detail"><a href="#">Lihat Detail</a></div>
          </div>
          <div class="foot">
            <p>Penerbangan Ke <strong>Bali Denpasar</strong></p>
            <p>Mulai dari Rp 403.264</p>
          </div>
        </div>
        <div class="card-image2">
          <div class="head">
            <div class="logo"><img src="images/logogaruda.png" alt="Garuda Indonesia"></div>
            <div class="detail"><a href="#">Lihat Detail</a></div>
          </div>
          <div class="foot">
            <p>Penerbangan ke <strong>Bali - Denpasar</strong></p>
            <p>Mulai dari Rp 700.599</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    const roundTripToggle = document.getElementById('round-trip-toggle');
    const returnDateContainer = document.getElementById('return-date-container');
    const swapIcon = document.getElementById('swap-icon');
    const fromGroup = document.getElementById('from-group');
    const toGroup = document.getElementById('to-group');

    let isRoundTrip = true;

    // Round trip toggle functionality
    function toggleReturnDate() {
        isRoundTrip = !isRoundTrip;
        const icon = roundTripToggle.querySelector('i');
        if (isRoundTrip) {
            returnDateContainer.style.display = 'block';
            icon.className = 'fas fa-check';
        } else {
            returnDateContainer.style.display = 'none';
            icon.className = 'fas fa-times';
        }
    }
    roundTripToggle.addEventListener('click', toggleReturnDate);

    // Swap functionality
    swapIcon.addEventListener('click', () => {
        // Add animation classes
        swapIcon.classList.add('swapping');
        fromGroup.classList.add('swapping');
        toGroup.classList.add('swapping');

        setTimeout(() => {
            // Swap the content
            const fromWrapper = fromGroup.querySelector('.input-wrapper');
            const toWrapper = toGroup.querySelector('.input-wrapper');
            const temp = fromWrapper.innerHTML;
            fromWrapper.innerHTML = toWrapper.innerHTML;
            toWrapper.innerHTML = temp;

            // Remove animation classes
            swapIcon.classList.remove('swapping');
            fromGroup.classList.remove('swapping');
            toGroup.classList.remove('swapping');
        }, 200); // Duration should match the CSS transition
    });

    // Initial state for round trip
    isRoundTrip = false;
    toggleReturnDate();

  </script>

</body>

</html>
