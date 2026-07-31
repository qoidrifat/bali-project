<?php
require_once __DIR__ . '/../includes/helpers.php';

function booking_platform_cards($type)
{
    $platforms = [
        'bus' => [
            [
                'name' => 'Traveloka',
                'desc' => 'Cek opsi bus dan shuttle antar kota.',
                'url' => 'https://www.traveloka.com/id-id/bus-and-shuttle',
                'domain' => 'traveloka.com',
                'tag' => 'Bus & shuttle',
            ],
            [
                'name' => 'tiket.com',
                'desc' => 'Bandingkan jadwal bus, shuttle, dan transport lokal.',
                'url' => 'https://www.tiket.com/',
                'domain' => 'tiket.com',
                'tag' => 'OTA Indonesia',
            ],
            [
                'name' => 'redBus',
                'desc' => 'Cari operator bus lain yang mungkin tersedia.',
                'url' => 'https://www.redbus.com/Home/',
                'domain' => 'redbus.com',
                'tag' => 'Bus ticketing',
            ],
        ],
        'hotel' => [
            [
                'name' => 'Traveloka',
                'desc' => 'Cari hotel dan akomodasi di area tujuan.',
                'url' => 'https://www.traveloka.com/id-id/hotel',
                'domain' => 'traveloka.com',
                'tag' => 'Hotel lokal',
            ],
            [
                'name' => 'Agoda',
                'desc' => 'Bandingkan hotel, villa, dan penginapan Bali.',
                'url' => 'https://www.agoda.com/id-id/',
                'domain' => 'agoda.com',
                'tag' => 'Hotel deals',
            ],
            [
                'name' => 'Booking.com',
                'desc' => 'Lihat pilihan akomodasi dengan review tamu.',
                'url' => 'https://www.booking.com/',
                'domain' => 'booking.com',
                'tag' => 'Akomodasi',
            ],
        ],
        'flight' => [
            [
                'name' => 'Traveloka',
                'desc' => 'Cari tiket pesawat domestik dan internasional.',
                'url' => 'https://www.traveloka.com/id-id/flight',
                'domain' => 'traveloka.com',
                'tag' => 'Tiket pesawat',
            ],
            [
                'name' => 'tiket.com',
                'desc' => 'Bandingkan harga dan jadwal maskapai.',
                'url' => 'https://www.tiket.com/pesawat',
                'domain' => 'tiket.com',
                'tag' => 'Flight OTA',
            ],
            [
                'name' => 'Skyscanner',
                'desc' => 'Gunakan mesin pencari penerbangan pembanding.',
                'url' => 'https://www.skyscanner.com/flights',
                'domain' => 'skyscanner.com',
                'tag' => 'Flight search',
            ],
        ],
        'car' => [
            [
                'name' => 'Traveloka',
                'desc' => 'Cari sewa mobil dan transportasi perjalanan.',
                'url' => 'https://www.traveloka.com/id-id/car-rental',
                'domain' => 'traveloka.com',
                'tag' => 'Rental mobil',
            ],
            [
                'name' => 'tiket.com',
                'desc' => 'Cek opsi sewa mobil dari penyedia lain.',
                'url' => 'https://www.tiket.com/sewa-mobil',
                'domain' => 'tiket.com',
                'tag' => 'Car rental',
            ],
            [
                'name' => 'Klook',
                'desc' => 'Pesan transfer bandara atau private car di Bali.',
                'url' => 'https://www.klook.com/en-AU/airport-transfers/',
                'domain' => 'klook.com',
                'tag' => 'Transfer',
            ],
        ],
    ];

    return $platforms[$type] ?? [];
}

function render_booking_alternatives($type)
{
    $cards = booking_platform_cards($type);

    if (!$cards) {
        return;
    }
    ?>
    <section class="booking-alternatives" aria-label="Alternatif platform pemesanan">
        <div class="booking-alternatives__header">
            <span class="booking-alternatives__eyebrow">Alternatif pemesanan</span>
            <h2>Coba cek platform pemesanan lain</h2>
            <p>Database lokal belum menemukan pilihan yang cocok. Platform berikut bisa dipakai sebagai opsi lanjutan.</p>
        </div>

        <div class="booking-alternatives__grid">
            <?php foreach ($cards as $card) : ?>
                <a
                    class="booking-platform-card"
                    href="<?= e(safe_external_url($card['url'])) ?>"
                    target="_blank"
                    rel="noopener noreferrer"
                    aria-label="Buka <?= e($card['name']) ?> di tab baru"
                >
                    <span class="booking-platform-card__logo">
                        <img
                            src="https://www.google.com/s2/favicons?domain=<?= e($card['domain']) ?>&sz=64"
                            alt="Logo <?= e($card['name']) ?>"
                            loading="lazy"
                            referrerpolicy="no-referrer"
                        />
                    </span>
                    <span class="booking-platform-card__body">
                        <span class="booking-platform-card__tag"><?= e($card['tag']) ?></span>
                        <strong><?= e($card['name']) ?></strong>
                        <span><?= e($card['desc']) ?></span>
                    </span>
                    <span class="booking-platform-card__arrow" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M7 17 17 7"/>
                            <path d="M8 7h9v9"/>
                        </svg>
                    </span>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
    <?php
}
?>
