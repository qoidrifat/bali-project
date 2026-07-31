<?php
require_once __DIR__ . '/includes/auth.php';
auth_start_session();

include "connection.php";

if (!function_exists('detail_reviews_schema_ready')) {
  function detail_reviews_schema_ready($connection)
  {
    if (!$connection) {
      return false;
    }

    $result = mysqli_query($connection, "SHOW TABLES LIKE 'reviews'");

    if (!$result) {
      error_log("Review table check failed: " . mysqli_error($connection));
      return false;
    }

    $ready = mysqli_num_rows($result) > 0;
    mysqli_free_result($result);

    return $ready && auth_schema_ready($connection);
  }
}

if (!function_exists('detail_review_summary')) {
  function detail_review_summary($connection, $destinationId)
  {
    $summary = [
      'count' => 0,
      'average' => null,
    ];

    $stmt = mysqli_prepare($connection, "SELECT COUNT(*) AS total_reviews, AVG(rating) AS average_rating FROM reviews WHERE destination_id = ?");

    if (!$stmt) {
      error_log("Review summary prepare failed: " . mysqli_error($connection));
      return $summary;
    }

    mysqli_stmt_bind_param($stmt, "i", $destinationId);

    if (!mysqli_stmt_execute($stmt)) {
      error_log("Review summary execute failed: " . mysqli_stmt_error($stmt));
      mysqli_stmt_close($stmt);
      return $summary;
    }

    $result = mysqli_stmt_get_result($stmt);
    $row = $result ? mysqli_fetch_assoc($result) : null;
    mysqli_stmt_close($stmt);

    if ($row) {
      $summary['count'] = (int) ($row['total_reviews'] ?? 0);
      $summary['average'] = $row['average_rating'] !== null ? round((float) $row['average_rating'], 1) : null;
    }

    return $summary;
  }
}

if (!function_exists('detail_review_list')) {
  function detail_review_list($connection, $destinationId)
  {
    $reviews = [];
    $sql = "SELECT reviews.rating, reviews.review_text, reviews.created_at, reviews.updated_at, users.name AS reviewer_name
            FROM reviews
            INNER JOIN users ON users.id = reviews.user_id
            WHERE reviews.destination_id = ?
            ORDER BY COALESCE(reviews.updated_at, reviews.created_at) DESC
            LIMIT 12";

    $stmt = mysqli_prepare($connection, $sql);

    if (!$stmt) {
      error_log("Review list prepare failed: " . mysqli_error($connection));
      return $reviews;
    }

    mysqli_stmt_bind_param($stmt, "i", $destinationId);

    if (!mysqli_stmt_execute($stmt)) {
      error_log("Review list execute failed: " . mysqli_stmt_error($stmt));
      mysqli_stmt_close($stmt);
      return $reviews;
    }

    $result = mysqli_stmt_get_result($stmt);

    while ($result && $row = mysqli_fetch_assoc($result)) {
      $reviews[] = $row;
    }

    mysqli_stmt_close($stmt);

    return $reviews;
  }
}

$data = null;
$detail_images = [];
$reviews = [];
$review_summary = [
  'count' => 0,
  'average' => null,
];
$review_errors = [];
$review_success = isset($_GET['review']) && $_GET['review'] === 'success';
$review_schema_ready = false;
$error_message = null;

$id_param = $_GET["id"] ?? null;

if ($id_param === null || $id_param === "") {
  http_response_code(400);
  $error_message = "ID destinasi belum diberikan.";
} elseif (!ctype_digit((string) $id_param) || (int) $id_param < 1) {
  http_response_code(400);
  $error_message = "ID destinasi tidak valid.";
} elseif (!$connection) {
  http_response_code(500);
  $error_message = "Koneksi database tidak tersedia.";
} else {
  $id = (int) $id_param;
  $conditions = ["id_des = ?"];

  if (db_column_exists($connection, 'destination', 'is_active')) {
    $conditions[] = "destination.is_active = 1";
  }

  if (db_column_exists($connection, 'destination', 'deleted_at')) {
    $conditions[] = "destination.deleted_at IS NULL";
  }

  $sql = "SELECT * FROM detail INNER JOIN destination USING(id_des) WHERE " . implode(" AND ", $conditions);
  $stmt = mysqli_prepare($connection, $sql);

  if (!$stmt) {
    http_response_code(500);
    $error_message = "Query detail tidak dapat disiapkan.";
  } else {
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (!mysqli_stmt_execute($stmt)) {
      http_response_code(500);
      $error_message = "Data detail gagal dimuat.";
    } else {
      $result = mysqli_stmt_get_result($stmt);
      $data = $result ? mysqli_fetch_assoc($result) : null;

      if (!$data) {
        http_response_code(404);
        $error_message = "Destinasi tidak ditemukan.";
      }
    }

    mysqli_stmt_close($stmt);
  }

  if ($data) {
    $review_schema_ready = detail_reviews_schema_ready($connection);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'submit_review') {
      if (!auth_check()) {
        $review_errors[] = "Anda harus login untuk memberi review.";
      } elseif (!$review_schema_ready) {
        $review_errors[] = "Fitur review belum aktif. Jalankan migration reviews terlebih dahulu.";
      } elseif (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $review_errors[] = "Sesi form tidak valid. Muat ulang halaman lalu coba lagi.";
      }

      $rating = trim($_POST['rating'] ?? '');
      $review_text = trim($_POST['review_text'] ?? '');

      if (!in_array($rating, ['1', '2', '3', '4', '5'], true)) {
        $review_errors[] = "Rating wajib dipilih antara 1 sampai 5.";
      }

      if ($review_text === '' || strlen($review_text) < 10) {
        $review_errors[] = "Review wajib diisi minimal 10 karakter.";
      }

      if (strlen($review_text) > 1000) {
        $review_errors[] = "Review maksimal 1000 karakter.";
      }

      if (!$review_errors) {
        $user = auth_user();
        $user_id = (int) $user['id'];
        $rating_value = (int) $rating;

        $review_stmt = mysqli_prepare(
          $connection,
          "INSERT INTO reviews (destination_id, user_id, rating, review_text)
           VALUES (?, ?, ?, ?)
           ON DUPLICATE KEY UPDATE rating = VALUES(rating), review_text = VALUES(review_text), updated_at = NOW()"
        );

        if (!$review_stmt) {
          error_log("Review submit prepare failed: " . mysqli_error($connection));
          $review_errors[] = "Review belum bisa disimpan. Silakan coba lagi nanti.";
        } else {
          mysqli_stmt_bind_param($review_stmt, "iiis", $id, $user_id, $rating_value, $review_text);

          if (!mysqli_stmt_execute($review_stmt)) {
            error_log("Review submit execute failed: " . mysqli_stmt_error($review_stmt));
            $review_errors[] = "Review belum bisa disimpan. Silakan coba lagi nanti.";
          }

          mysqli_stmt_close($review_stmt);
        }

        if (!$review_errors) {
          header("Location: detail.php?id=" . $id . "&review=success#reviews");
          exit;
        }
      }
    }

    $id_detail = (int) $data["id_detail"];
    $image_stmt = mysqli_prepare($connection, "SELECT gambar FROM detail_image WHERE id_detail = ?");

    if (!$image_stmt) {
      http_response_code(500);
      $error_message = "Query gambar tidak dapat disiapkan.";
      $data = null;
    } else {
      mysqli_stmt_bind_param($image_stmt, "i", $id_detail);

      if (!mysqli_stmt_execute($image_stmt)) {
        http_response_code(500);
        $error_message = "Data gambar gagal dimuat.";
        $data = null;
      } else {
        $image_result = mysqli_stmt_get_result($image_stmt);

        while ($image_result && $data_image = mysqli_fetch_assoc($image_result)) {
          $detail_images[] = $data_image;
        }
      }

      mysqli_stmt_close($image_stmt);
    }

    if ($data && $review_schema_ready) {
      $review_summary = detail_review_summary($connection, $id);
      $reviews = detail_review_list($connection, $id);
    }
  }
}

  $page_title = $data['nama_des'] ?? 'Detail Destinasi';
  $page_desc  = 'Detail destinasi wisata Bali.';
  $page_css   = 'styles/detail.css';
  $active     = 'destination';
  include 'partials/head.php';
  include 'partials/navbar.php';
?>
  <div class="wrapper">
    <?php if ($error_message): ?>
      <div class="detail">
        <div class="detail_content">
          <h1>Detail tidak tersedia</h1>
          <p><?= e($error_message) ?></p>
          <p><a href="destination.php">Kembali ke Destination</a></p>
        </div>
      </div>
    <?php else: ?>
      <?php $bannerImage = public_image_path($data["gambar"]); ?>
      <div class="banner" style="background: linear-gradient(to right, rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0.5)),url('<?= e($bannerImage) ?>');background-size: cover; background-position: center">
        <h1><?= e($data['nama_des']); ?></h1>
      </div>

      <div class="detail">
        <div class="detail_content">
          <?= sanitize_html_fragment($data["desc"]); ?>
        </div>
        <div class="detail_image">
          <?php foreach ($detail_images as $data_image) { ?>
            <?php $detailImage = public_image_path($data_image['gambar']); ?>
            <img src="<?= e($detailImage); ?>" alt="thumbnail"<?= image_dimensions_attr($detailImage) ?> loading="lazy" decoding="async" />
          <?php } ?>
        </div>
      </div>

      <section class="reviews" id="reviews" aria-labelledby="reviews-title">
        <div class="reviews__header">
          <div>
            <span class="reviews__eyebrow">Review Destinasi</span>
            <h2 id="reviews-title">Rating & Review</h2>
            <p>Bagikan pengalaman Anda setelah mengunjungi destinasi ini.</p>
          </div>

          <div class="reviews__summary" aria-label="Rata-rata rating">
            <strong><?= $review_summary['average'] !== null ? e(number_format($review_summary['average'], 1, ',', '.')) : '-' ?></strong>
            <span>
              <?php for ($i = 1; $i <= 5; $i++): ?>
                <span class="<?= $review_summary['average'] !== null && $i <= round($review_summary['average']) ? 'is-filled' : '' ?>">&#9733;</span>
              <?php endfor; ?>
            </span>
            <small><?= e((string) $review_summary['count']) ?> review</small>
          </div>
        </div>

        <?php if (!$review_schema_ready): ?>
          <div class="reviews__notice">
            Fitur review belum aktif. Review file migration <code>database/2026_06_09_create_reviews_table.sql</code>, lalu jalankan manual.
          </div>
        <?php elseif ($review_success): ?>
          <div class="reviews__success" role="status">Review Anda berhasil disimpan.</div>
        <?php endif; ?>

        <?php if ($review_errors): ?>
          <div class="reviews__errors" role="alert">
            <strong>Review belum bisa disimpan.</strong>
            <ul>
              <?php foreach (array_unique($review_errors) as $review_error): ?>
                <li><?= e($review_error) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <div class="reviews__grid">
          <div class="reviews__form-card">
            <?php if (!auth_check()): ?>
              <h3>Login untuk memberi review</h3>
              <p>Hanya user login yang dapat memberi rating dan review destinasi.</p>
              <a class="reviews__button" href="login.php">Login</a>
            <?php else: ?>
              <h3>Tulis review Anda</h3>
              <form action="detail.php?id=<?= (int) $data['id_des'] ?>#reviews" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="submit_review">

                <div class="reviews__field">
                  <label for="rating">Rating</label>
                  <select id="rating" name="rating" required>
                    <option value="">Pilih rating</option>
                    <option value="5">5 - Sangat baik</option>
                    <option value="4">4 - Baik</option>
                    <option value="3">3 - Cukup</option>
                    <option value="2">2 - Kurang</option>
                    <option value="1">1 - Buruk</option>
                  </select>
                </div>

                <div class="reviews__field">
                  <label for="review_text">Review</label>
                  <textarea id="review_text" name="review_text" rows="5" maxlength="1000" required placeholder="Tulis pengalaman Anda minimal 10 karakter"></textarea>
                </div>

                <button class="reviews__button" type="submit">Kirim Review</button>
              </form>
            <?php endif; ?>
          </div>

          <div class="reviews__list">
            <?php if (!$review_schema_ready): ?>
              <article class="reviews__empty">Belum ada review karena tabel review belum dibuat.</article>
            <?php elseif (!$reviews): ?>
              <article class="reviews__empty">Belum ada review untuk destinasi ini.</article>
            <?php else: ?>
              <?php foreach ($reviews as $review): ?>
                <article class="reviews__item">
                  <div class="reviews__item-head">
                    <strong><?= e($review['reviewer_name']) ?></strong>
                    <span>
                      <?php for ($i = 1; $i <= 5; $i++): ?>
                        <span class="<?= $i <= (int) $review['rating'] ? 'is-filled' : '' ?>">&#9733;</span>
                      <?php endfor; ?>
                    </span>
                  </div>
                  <p><?= e($review['review_text']) ?></p>
                  <small><?= e($review['updated_at'] ?: $review['created_at']) ?></small>
                </article>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>
      </section>
    <?php endif; ?>
  </div>
<?php include 'partials/footer.php'; ?>
