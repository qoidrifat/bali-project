<?php
include "connection.php";

$errors = [];
$result = null;
$stmt = null;
$conn = $connection;

$from = trim($_GET['from'] ?? '');
$to = trim($_GET['to'] ?? '');
$departureDate = trim($_GET['departure-date'] ?? '');
$returnDate = trim($_GET['return-date'] ?? '');
$passengers = trim($_GET['passengers'] ?? '');

$city_options = [
    '1' => 'Surabaya',
    '2' => 'Denpasar',
];

if (!array_key_exists($from, $city_options)) {
    $errors[] = 'Pilih kota asal yang valid.';
}

if (!array_key_exists($to, $city_options)) {
    $errors[] = 'Pilih kota tujuan yang valid.';
}

if ($from !== '' && $to !== '' && $from === $to) {
    $errors[] = 'Kota asal dan tujuan tidak boleh sama.';
}

if ($departureDate === '' || !is_valid_date_value($departureDate)) {
    $errors[] = 'Tanggal keberangkatan wajib diisi dengan format yang valid.';
}

if ($returnDate !== '' && !is_valid_date_value($returnDate)) {
    $errors[] = 'Tanggal pulang tidak valid.';
}

if ($departureDate !== '' && $returnDate !== '' && is_valid_date_value($departureDate) && is_valid_date_value($returnDate) && $returnDate < $departureDate) {
    $errors[] = 'Tanggal pulang tidak boleh sebelum tanggal keberangkatan.';
}

if (!in_array($passengers, ['1', '2'], true)) {
    $errors[] = 'Pilih jumlah kursi yang valid.';
}

$from_name = $city_options[$from] ?? '-';
$to_name = $city_options[$to] ?? '-';

if (!$errors) {
    if (!$conn) {
        $errors[] = 'Maaf, koneksi database sedang bermasalah. Silakan coba lagi nanti.';
    } else {
        $from_id = (int) $from;
        $to_id = (int) $to;
        $seat_count = (int) $passengers;

        $sql = "SELECT pesawat.nama_pesawat, bookings_pesawat.detail
                FROM pesawat
                JOIN bookings_pesawat ON pesawat.id_pesawat = bookings_pesawat.id_pesawat
                WHERE bookings_pesawat.from_id = ?
                AND bookings_pesawat.to_id = ?
                AND bookings_pesawat.departure_date = ?";

        if ($returnDate !== '') {
            $sql .= " AND bookings_pesawat.return_date = ?";
        }

        $sql .= " AND bookings_pesawat.jumlah_kursi = ?";

        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            error_log('Statement prepare failed in hasil.pesawat.php: ' . $conn->error);
            $errors[] = 'Maaf, pencarian tiket pesawat belum bisa diproses.';
        } else {
            if ($returnDate !== '') {
                $stmt->bind_param('iissi', $from_id, $to_id, $departureDate, $returnDate, $seat_count);
            } else {
                $stmt->bind_param('iisi', $from_id, $to_id, $departureDate, $seat_count);
            }

            if (!$stmt->execute()) {
                error_log('Statement execute failed in hasil.pesawat.php: ' . $stmt->error);
                $errors[] = 'Maaf, pencarian tiket pesawat gagal diproses.';
            } else {
                $result = $stmt->get_result();
            }
        }
    }
}
    $page_title = 'Hasil Pencarian Tiket Pesawat';
    $page_desc  = 'Hasil pencarian tiket pesawat berdasarkan rute, tanggal, dan jumlah kursi.';
    $page_css   = 'styles/hasil.bus.css';
    $active     = 'transport';

    include_once 'partials/booking_alternatives.php';
    include 'partials/head.php';
    include 'partials/navbar.php';
?>
    <div class="results">
        <div class="main-content">
            <div class="header">
                <a class="back-button" href="transport.php">Kembali</a>
                <h1>Hasil Pencarian Tiket Pesawat</h1>
            </div>

            <div class="search-details">
                <p><strong>Dari:</strong> <?= htmlspecialchars($from_name) ?></p>
                <p><strong>Ke:</strong> <?= htmlspecialchars($to_name) ?></p>
                <p><strong>Pergi:</strong> <?= htmlspecialchars($departureDate) ?></p>
                <?php if (!empty($returnDate)) : ?>
                    <p><strong>Pulang:</strong> <?= htmlspecialchars($returnDate) ?></p>
                <?php endif; ?>
                <p><strong>Jumlah Kursi:</strong> <?= htmlspecialchars($passengers) ?></p>
            </div>

            <div class="bus-results">
                <?php if ($errors) : ?>
                    <?php foreach ($errors as $error) : ?>
                        <p><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                <?php elseif ($result && $result->num_rows > 0) : ?>
                    <table>
                        <thead>
                            <tr>
                                <th align="center">Maskapai</th>
                                <th align="center">Informasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()) : ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['nama_pesawat']) ?></td>
                                    <td><a href="<?= e(safe_external_url($row['detail'])) ?>" target="_blank" rel="noopener noreferrer">Lihat Selengkapnya</a></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <p>Tidak ditemukan hasil untuk pencarian Anda.</p>
                    <?php render_booking_alternatives('flight'); ?>
                <?php endif; ?>
            </div>

            <?php
            if ($stmt) {
                $stmt->close();
            }

            ?>
        </div>
    </div>
<?php include 'partials/footer.php'; ?>
