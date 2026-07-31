<?php
$page_title = 'Tiket Bus';
$page_desc  = 'Cari tiket bus berdasarkan kota asal, tujuan, tanggal, dan jumlah kursi.';
$page_css   = 'styles/tiket.bus.css';
$active     = 'tiket';
$extra_head = <<<'HTML'
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
HTML;
include 'partials/head.php';
include 'partials/navbar.php';
?>
  <div class="tiket">
    <div class="main-content">
      <div class="bus-ticket-section">
        <div class="bus-image">
          <img src="images/optimized/tiket/bus.webp" alt="Bus Image" width="1200" height="675" decoding="async" fetchpriority="high" />
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
                <div class="date-field">
                  <div class="date-label-row">
                    <label for="departure-date">Pergi</label>
                    <span class="trip-toggle">
                      <span>Pulang-Pergi?</span>
                      <label class="switch">
                        <input type="checkbox" id="return-switch">
                        <span class="slider"></span>
                      </label>
                    </span>
                  </div>
                  <input type="text" id="departure-date" name="departure-date" class="custom-input" placeholder="Pilih Tanggal">
                </div>

                <div id="return-date-container" class="date-field">
                  <label for="return-date">Pulang</label>
                  <input type="text" id="return-date" name="return-date" class="custom-input" placeholder="Pilih Tanggal Kembali" disabled>
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
      const returnSwitch = document.getElementById('return-switch');
      const returnDateContainer = document.getElementById('return-date-container');
      const returnInputRaw = document.getElementById('return-date');
      const calendarOverlay = document.getElementById('calendar-overlay');
      const modalCloseBtn = document.getElementById('modal-close');
      const flatpickrContainer = document.getElementById('flatpickr-container');
      const calendarTitle = document.getElementById('calendar-title');

      if (!window.flatpickr || !returnSwitch || !returnDateContainer || !calendarOverlay) {
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
            if (!instance._input.disabled) {
              const type = instance.element.id === 'departure-date' ? 'departure' : 'return';
              openModalCalendar(instance, type);
            }
          });
        }
      };

      const departureInputInstance = flatpickr("#departure-date", {
        ...inputConfig,
        defaultDate: simulatedToday
      });

      const returnInputInstance = flatpickr("#return-date", {
        ...inputConfig
      });

      returnDateContainer.style.opacity = '0.5';

      returnSwitch.addEventListener('change', function() {
        if (this.checked) {
          returnDateContainer.style.opacity = '1';
          returnInputRaw.removeAttribute('disabled');
          if (returnInputInstance) {
            returnInputInstance._input.disabled = false;
            setTimeout(function() {
              openModalCalendar(returnInputInstance, 'return');
            }, 100);
          }
        } else {
          returnDateContainer.style.opacity = '0.5';
          returnInputRaw.setAttribute('disabled', 'disabled');
          if (returnInputInstance) {
            returnInputInstance.clear();
            returnInputInstance._input.disabled = true;
          }
        }
      });

      function openModalCalendar(targetInputInstance, type) {
        calendarOverlay.classList.add('active');
        if (calendarTitle) {
          calendarTitle.innerText = type === 'departure' ? 'Pilih Tanggal Pergi' : 'Pilih Tanggal Pulang';
        }

        let minDateConfig = simulatedToday;
        if (type === 'return' && departureInputInstance.selectedDates.length > 0) {
          const deptDate = departureInputInstance.selectedDates[0];
          const simDate = new Date(simulatedToday);
          minDateConfig = deptDate > simDate ? deptDate : simulatedToday;
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
      calendarOverlay.addEventListener('click', function(e) {
        if (e.target === calendarOverlay) {
          closeModal();
        }
      });
    });
  </script>
<?php include 'partials/footer.php'; ?>
