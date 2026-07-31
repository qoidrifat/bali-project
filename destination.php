<?php

include "connection.php";

$destinations = [];
$error_message = null;

if (!$connection) {
  http_response_code(500);
  $error_message = "Koneksi database tidak tersedia. Silakan coba lagi nanti.";
} else {
  $conditions = [];

  if (db_column_exists($connection, 'destination', 'is_active')) {
    $conditions[] = "is_active = 1";
  }

  if (db_column_exists($connection, 'destination', 'deleted_at')) {
    $conditions[] = "deleted_at IS NULL";
  }

  $sql = "SELECT * FROM destination";

  if ($conditions) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
  }

  $query = mysqli_query($connection, $sql);

  if (!$query) {
    http_response_code(500);
    error_log("Destination query failed: " . mysqli_error($connection));
    $error_message = "Data destinasi belum bisa dimuat. Silakan coba lagi nanti.";
  } else {
    while ($data = mysqli_fetch_assoc($query)) {
      $destinations[] = $data;
    }
  }
}

$page_title = 'Destinations';
$page_desc  = 'Jelajahi destinasi wisata utama di Bali.';
$page_css   = 'styles/destination.css';
$active     = 'destination';
include 'partials/head.php';
include 'partials/navbar.php';

?>

  <div class="wrapper">
    <h1 class="page-title">Explore Our Destinations</h1>
    <div class="destination">
      <?php if ($error_message): ?>
        <p><?= e($error_message) ?></p>
      <?php elseif (empty($destinations)): ?>
        <p>Belum ada destinasi yang tersedia.</p>
      <?php else: ?>
      <?php foreach ($destinations as $data) { ?>
        <div class="card_destination">
          <a href="detail.php?id=<?= (int) $data['id_des'] ?>">
            <?php $destinationImage = public_image_path($data['gambar']); ?>
            <img src="<?= e($destinationImage); ?>" alt="<?= e($data['nama_des']) ?>"<?= image_dimensions_attr($destinationImage) ?> loading="lazy" decoding="async" />
            <div>
              <h2><?= e($data["nama_des"]) ?></h2>
            </div>
          </a>
        </div>
      <?php } ?>
      <?php endif; ?>
    </div>
  </div>
<?php include 'partials/footer.php'; ?>
