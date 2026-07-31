<?php
include "connection.php";

$errors = [];
$result = null;
$stmt = null;
$conn = $connection;

$destination = trim($_GET['destination'] ?? '');
$check_in = trim($_GET['check-in-date'] ?? '');
$check_out = trim($_GET['check-out-date'] ?? '');
$rooms = trim($_GET['rooms'] ?? '');

$destination_options = [
    '1' => 'Surabaya',
    '2' => 'Denpasar',
];

if (!array_key_exists($destination, $destination_options)) {
    $errors[] = 'Pilih kota tujuan yang valid.';
}

if ($check_in === '' || !is_valid_date_value($check_in)) {
    $errors[] = 'Tanggal sewa wajib diisi dengan format yang valid.';
}

if ($check_out !== '' && !is_valid_date_value($check_out)) {
    $errors[] = 'Tanggal selesai tidak valid.';
}

if ($check_in !== '' && $check_out !== '' && is_valid_date_value($check_in) && is_valid_date_value($check_out) && $check_out < $check_in) {
    $errors[] = 'Tanggal selesai tidak boleh sebelum tanggal sewa.';
}

if (!in_array($rooms, ['1', '2'], true)) {
    $errors[] = 'Pilih jumlah mobil yang valid.';
}

$destination_name = $destination_options[$destination] ?? '-';

if (!$errors) {
    if (!$conn) {
        $errors[] = 'Maaf, koneksi database sedang bermasalah. Silakan coba lagi nanti.';
    } else {
        $destination_id = (int) $destination;
        $rooms_count = (int) $rooms;

        $sql = "SELECT car.vendor, car.name, bookings_mobil.detail
                FROM car
                JOIN bookings_mobil ON car.id_car = bookings_mobil.id_car
                WHERE bookings_mobil.id_destinations = ?
                AND bookings_mobil.check_in_date = ?";

        if ($check_out !== '') {
            $sql .= " AND bookings_mobil.check_out_date = ?";
        }

        $sql .= " AND bookings_mobil.rooms = ?";

        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            error_log('Statement prepare failed in hasil.mobil.php: ' . $conn->error);
            $errors[] = 'Maaf, pencarian mobil belum bisa diproses.';
        } else {
            if ($check_out !== '') {
                $stmt->bind_param('issi', $destination_id, $check_in, $check_out, $rooms_count);
            } else {
                $stmt->bind_param('isi', $destination_id, $check_in, $rooms_count);
            }

            if (!$stmt->execute()) {
                error_log('Statement execute failed in hasil.mobil.php: ' . $stmt->error);
                $errors[] = 'Maaf, pencarian mobil gagal diproses.';
            } else {
                $result = $stmt->get_result();
            }
        }
    }
}
    $page_title = 'Hasil Pencarian Mobil';
    $page_desc  = 'Hasil pencarian rental mobil berdasarkan tujuan, tanggal, dan jumlah mobil.';
    $page_css   = 'styles/hasil.mobil.css';
    $active     = 'tiket';

    include_once 'partials/booking_alternatives.php';
    include 'partials/head.php';
    include 'partials/navbar.php';
?>

    <div class="results">
        <div class="main-content">
            <div class="header">
                <a class="back-button" href="sewa.mobil.php">Kembali</a>
                <h1>Hasil Pencarian Mobil</h1>
            </div>

            <div class="search-details">
                <p><strong>Tujuan:</strong> <?= htmlspecialchars($destination_name) ?></p>
                <p><strong>Check-In:</strong> <?= htmlspecialchars($check_in) ?></p>
                <p><strong>Check-Out:</strong> <?= htmlspecialchars($check_out) ?></p>
                <p><strong>Jumlah Mobil:</strong> <?= htmlspecialchars($rooms) ?></p>
            </div>

            <div class="car-results">
                <?php if ($errors): ?>
                    <?php foreach ($errors as $error): ?>
                        <p><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                <?php elseif ($result && $result->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th align="center">Vendor</th>
                                <th align="center">Mobil</th>
                                <th align="center">Informasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['vendor']) ?></td>
                                    <td><?= htmlspecialchars($row['name']) ?></td>
                                    <td><a href="<?= e(safe_external_url($row['detail'])) ?>" target="_blank" rel="noopener noreferrer">Lihat Selengkapnya</a></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>Tidak ditemukan hasil untuk pencarian Anda.</p>
                    <?php render_booking_alternatives('car'); ?>
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
