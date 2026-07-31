<?php
$page_title = 'Sewa Mobil';
$page_desc  = 'Cari rental mobil berdasarkan kota tujuan, tanggal sewa, dan jumlah mobil.';
$page_css   = 'styles/booking.hotel.css';
$active     = 'tiket';
$extra_head = <<<'HTML'
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
HTML;
include 'partials/head.php';
include 'partials/navbar.php';
?>
  <div class="booking">
    <div class="main-content">
      <div class="hotel-booking-section">
        <div class="hotel-image">
          <img src="images/optimized/tiket/rental.webp" alt="Car Rental Image" width="1920" height="1453" decoding="async" fetchpriority="high" />
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
                <div class="date-field">
                  <label for="check-in-date">Tanggal Sewa</label>
                  <input type="text" id="check-in-date" name="check-in-date" class="custom-input" placeholder="Pilih Tanggal Sewa">
                </div>

                <div class="date-field">
                  <label for="check-out-date">Tanggal Selesai</label>
                  <input type="text" id="check-out-date" name="check-out-date" class="custom-input" placeholder="Pilih Tanggal Selesai">
                </div>
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

  <div class="calendar-modal-overlay" id="calendar-overlay">
    <div class="calendar-modal-container">
      <button class="modal-close-btn" id="modal-close" type="button" aria-label="Tutup kalender">&times;</button>
      <h3 id="calendar-title">Pilih Tanggal</h3>
      <div id="flatpickr-container"></div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const calendarOverlay = document.getElementById('calendar-overlay');
      const modalCloseBtn = document.getElementById('modal-close');
      const flatpickrContainer = document.getElementById('flatpickr-container');
      const calendarTitle = document.getElementById('calendar-title');

      if (!window.flatpickr || !calendarOverlay) {
        return;
      }

      let modalPickerInstance = null;
      const simulatedToday = "2026-02-07";

      const inputConfig = {
        altInput: true,
        altFormat: "l, j F Y",
        dateFormat: "Y-m-d",
        locale: "id",
        disableMobile: true,
        clickOpens: false,
        onReady: function(selectedDates, dateStr, instance) {
          instance.altInput.addEventListener('click', function() {
            const type = instance.element.id === 'check-in-date' ? 'start' : 'end';
            openModalCalendar(instance, type);
          });
        }
      };

      const startInputInstance = flatpickr("#check-in-date", {
        ...inputConfig,
        defaultDate: simulatedToday
      });

      const endInputInstance = flatpickr("#check-out-date", {
        ...inputConfig
      });

      function openModalCalendar(targetInputInstance, type) {
        calendarOverlay.classList.add('active');
        if (calendarTitle) {
          calendarTitle.innerText = type === 'start' ? 'Pilih Tanggal Sewa' : 'Pilih Tanggal Selesai';
        }

        let minDateConfig = simulatedToday;
        if (type === 'end' && startInputInstance.selectedDates.length > 0) {
          const startDate = startInputInstance.selectedDates[0];
          const simDate = new Date(simulatedToday);
          minDateConfig = startDate > simDate ? startDate : simulatedToday;
        }

        if (modalPickerInstance) {
          modalPickerInstance.destroy();
          flatpickrContainer.innerHTML = '';
        }

        modalPickerInstance = flatpickr(flatpickrContainer, {
          locale: "id",
          inline: true,
          defaultDate: targetInputInstance.selectedDates[0] || undefined,
          minDate: minDateConfig,
          onDayCreate: function(dObj, dStr, fp, dayElem) {
            const dateStr = dayElem.dateObj.toISOString().split('T')[0];
            if (dateStr === simulatedToday) {
              dayElem.style.border = "2px solid #5aaecb";
              dayElem.style.fontWeight = "bold";
              dayElem.title = "Hari Ini (Simulasi)";
            }
          },
          onChange: function(selectedDates) {
            targetInputInstance.setDate(selectedDates[0], true);
            if (type === 'start' && endInputInstance.selectedDates.length > 0) {
              if (endInputInstance.selectedDates[0] < selectedDates[0]) {
                endInputInstance.clear();
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
      calendarOverlay.addEventListener('click', function(e) {
        if (e.target === calendarOverlay) {
          closeModal();
        }
      });
    });
  </script>
<?php include 'partials/footer.php'; ?>
