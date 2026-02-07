<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Transport - Cek Tiket Pesawat</title>
  
  <link rel="stylesheet" href="styles/transport.css">
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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

  <div class="main-content">
    
    <div class="hero-section">
      <div class="hero-text">
        <h1>Cek Harga Tiket Pesawat Online Murah dan Promo Hari Ini</h1>
      </div>

      <div class="search-card">
        <form action="hasil.pesawat.php" method="GET">
          
          <div class="form-grid-top">
            <div class="input-box vertical-group">
              <div class="group-item">
                <label for="from">Dari</label>
                <select id="from" name="from" class="custom-select">
                  <option value="" selected>Pilih Kota Asal</option>
                  <option value="1">Surabaya (SUB)</option>
                  <option value="2">Denpasar (DPS)</option>
                </select>
              </div>
              
              <div class="divider"></div>
              
              <div class="group-item">
                <label for="to">Ke</label>
                <select id="to" name="to" class="custom-select">
                  <option value="" selected>Pilih Kota Tujuan</option>
                  <option value="2">Denpasar (DPS)</option>
                  <option value="1">Surabaya (SUB)</option>
                </select>
              </div>
              
              <div class="swap-btn" id="swap-icon">
                <i class="fas fa-exchange-alt fa-rotate-90"></i>
              </div>
            </div>

            <div class="input-box vertical-group">
              <div class="group-item">
                <div class="row-space">
                    <label for="departure-date">Pergi</label>
                    <div class="toggle-container">
                        <span class="toggle-label">Pulang-Pergi?</span>
                        <label class="switch-custom">
                            <input type="checkbox" id="return-switch">
                            <span class="slider-round">
                            </span>
                        </label>
                    </div>
                </div>
                <input type="text" id="departure-date" name="departure-date" class="custom-input" placeholder="Pilih Tanggal">
              </div>

              <div class="divider"></div>
              
              <div class="group-item" id="return-date-wrapper">
                <label for="return-date">Pulang</label>
                <input type="text" id="return-date" name="return-date" class="custom-input" placeholder="Pilih Tanggal Kembali" disabled>
              </div>
            </div>
          </div>

          <div class="input-box single-row">
            <select id="passengers" name="passengers" class="custom-select full-width">
              <option value="" selected>Pilih Jumlah Penumpang</option>
              <option value="1">1 Penumpang, Ekonomi</option>
              <option value="2">2 Penumpang, Ekonomi</option>
            </select>
          </div>

          <button type="submit" class="btn-search">Ayo Cari</button>
        </form>
      </div>
    </div>

    <div class="recommendations">
      <h2>Rekomendasi Penerbangan Ekslusif</h2>
      <div class="cards-grid">
        <div class="promo-card card-lion">
          <div class="overlay"></div>
          <div class="card-content">
            <div class="card-top">
              <img src="images/lion.png" alt="Lion Air" class="airline-logo">
              <a href="#" class="btn-detail">Lihat Detail</a>
            </div>
            <div class="card-bottom">
              <div class="route-info">
                <span class="small-label">Penerbangan Ke</span>
                <span class="destination-name">Bali Denpasar</span>
              </div>
              <div class="price-info">Mulai dari Rp 403.264</div>
            </div>
          </div>
        </div>
        <div class="promo-card card-garuda">
          <div class="overlay"></div>
          <div class="card-content">
            <div class="card-top">
              <img src="images/logogaruda.png" alt="Garuda Indonesia" class="airline-logo">
              <a href="#" class="btn-detail">Lihat Detail</a>
            </div>
            <div class="card-bottom">
              <div class="route-info">
                <span class="small-label">Penerbangan ke</span>
                <span class="destination-name">Bali - Denpasar</span>
              </div>
              <div class="price-info">Mulai dari Rp 700.599</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="calendar-modal-overlay" id="calendar-overlay">
    <div class="calendar-modal-container">
        <button class="modal-close-btn" id="modal-close" type="button">
            <i class="fas fa-times"></i>
        </button>
        <h3 id="calendar-title" style="margin-bottom:15px; text-align:center; font-size:18px; font-weight:700;">Pilih Tanggal</h3>
        <div id="flatpickr-container"></div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>

  <script>
    // --- 1. Swap Logic ---
    const swapBtn = document.getElementById('swap-icon');
    const fromSelect = document.getElementById('from');
    const toSelect = document.getElementById('to');

    swapBtn.addEventListener('click', () => {
        const tempValue = fromSelect.value;
        fromSelect.value = toSelect.value;
        toSelect.value = tempValue;
        swapBtn.classList.add('rotating');
        setTimeout(() => swapBtn.classList.remove('rotating'), 300);
    });

    // --- 2. Logic Toggle Pulang-Pergi ---
    const returnSwitch = document.getElementById('return-switch');
    const returnDateWrapper = document.getElementById('return-date-wrapper');
    const returnInputRaw = document.getElementById('return-date');

    // State Awal
    returnDateWrapper.style.opacity = '0.5';

    returnSwitch.addEventListener('change', function() {
        if(this.checked) {
            returnDateWrapper.style.opacity = '1';
            returnInputRaw.removeAttribute('disabled');
            if(returnInputInstance) {
                returnInputInstance._input.disabled = false;
                // Auto open calendar
                setTimeout(() => {
                    openModalCalendar(returnInputInstance, 'return');
                }, 100);
            }
        } else {
            returnDateWrapper.style.opacity = '0.5';
            returnInputRaw.setAttribute('disabled', 'disabled');
            if(returnInputInstance) {
                returnInputInstance.clear();
                returnInputInstance._input.disabled = true;
            }
        }
    });

    // --- 3. LOGIC KALENDER (SIMULASI TANGGAL) ---
    const calendarOverlay = document.getElementById('calendar-overlay');
    const modalCloseBtn = document.getElementById('modal-close');
    const flatpickrContainer = document.getElementById('flatpickr-container');
    const calendarTitle = document.getElementById('calendar-title');
    
    let modalPickerInstance = null;

    // SIMULASI TANGGAL HARI INI: 7 FEBRUARI 2026
    const simulatedToday = "2026-02-07";

    // Config Input Form
    const inputConfig = {
        altInput: true,
        altFormat: "l, j F Y", 
        dateFormat: "Y-m-d",   
        locale: "id",
        disableMobile: "true",
        clickOpens: false,
        onReady: function(selectedDates, dateStr, instance) {
            instance.altInput.addEventListener('click', () => {
                if(!instance._input.disabled) {
                    const type = instance.element.id === 'departure-date' ? 'departure' : 'return';
                    openModalCalendar(instance, type);
                }
            });
        }
    };

    // Init Input Pergi
    const departureInputInstance = flatpickr("#departure-date", {
        ...inputConfig,
        defaultDate: simulatedToday
    });

    // Init Input Pulang
    const returnInputInstance = flatpickr("#return-date", {
        ...inputConfig
    });

    // Fungsi Buka Modal
    function openModalCalendar(targetInputInstance, type) {
        calendarOverlay.classList.add('active');
        
        // Judul Modal
        if(calendarTitle) {
             calendarTitle.innerText = type === 'departure' ? 'Pilih Tanggal Pergi' : 'Pilih Tanggal Pulang';
        }

        // Tentukan Min Date
        // Default: Tanggal 7 Feb 2026
        let minDateConfig = simulatedToday;

        // Jika Pulang, tidak boleh sebelum Pergi
        if (type === 'return' && departureInputInstance.selectedDates.length > 0) {
            const deptDate = departureInputInstance.selectedDates[0];
            const simDate = new Date(simulatedToday);
            // Pilih yang paling akhir antara Tgl Pergi vs Hari Ini
            minDateConfig = deptDate > simDate ? deptDate : simulatedToday;
        }

        // Hapus instance lama
        if (modalPickerInstance) {
            modalPickerInstance.destroy();
            flatpickrContainer.innerHTML = '';
        }

        // Render Kalender Baru
        modalPickerInstance = flatpickr(flatpickrContainer, {
            locale: "id",
            inline: true,
            defaultDate: targetInputInstance.selectedDates[0] || undefined,
            minDate: minDateConfig, // Terapkan batas tanggal
            
            // Penanda visual untuk "Hari Ini" (Simulasi)
            onDayCreate: function(dObj, dStr, fp, dayElem) {
                const dateStr = dayElem.dateObj.toISOString().split('T')[0];
                if (dateStr === simulatedToday) {
                    dayElem.style.border = "2px solid #5aaecb";
                    dayElem.style.fontWeight = "bold";
                    dayElem.title = "Hari Ini (Simulasi)";
                }
            },

            onChange: function(selectedDates, dateStr) {
                targetInputInstance.setDate(selectedDates[0], true);
                
                // Validasi Pulang-Pergi
                if (type === 'departure' && returnInputInstance.selectedDates.length > 0) {
                     if (returnInputInstance.selectedDates[0] < selectedDates[0]) {
                         returnInputInstance.clear();
                     }
                }
                setTimeout(closeModal, 200);
            }
        });
    }

    function closeModal() {
        calendarOverlay.classList.remove('active');
        if (modalPickerInstance) {
            modalPickerInstance.destroy();
            modalPickerInstance = null;
            flatpickrContainer.innerHTML = '';
        }
    }

    modalCloseBtn.addEventListener('click', closeModal);
    calendarOverlay.addEventListener('click', (e) => {
        if (e.target === calendarOverlay) closeModal();
    });
  </script>

</body>
</html>