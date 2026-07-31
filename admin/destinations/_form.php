<?php
$formAction = $formAction ?? 'admin/destinations/create.php';
$formMode = $formMode ?? 'create';
$submitLabel = $submitLabel ?? 'Simpan Destinasi';
$values = $values ?? [
    'nama_des' => '',
    'detail_text' => '',
    'gambar' => '',
];
?>

<form class="admin-form" action="<?= e($formAction) ?>" method="post" enctype="multipart/form-data" novalidate>
  <?= csrf_field() ?>

  <div class="admin-form__grid">
    <div class="admin-field">
      <label for="nama_des">Nama destinasi</label>
      <input
        id="nama_des"
        name="nama_des"
        type="text"
        value="<?= e($values['nama_des']) ?>"
        maxlength="100"
        required
      />
      <small>Contoh: Pantai Kuta, Pura Tanah Lot.</small>
    </div>

    <div class="admin-field">
      <label for="main_image">Gambar utama<?= $formMode === 'create' ? ' wajib' : ' baru' ?></label>
      <input
        id="main_image"
        name="main_image"
        type="file"
        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
        <?= $formMode === 'create' ? 'required' : '' ?>
      />
      <small>JPG, PNG, atau WebP. Maksimal 3 MB.</small>
    </div>
  </div>

  <?php if (!empty($values['gambar'])): ?>
    <div class="admin-current-image">
      <span>Gambar saat ini</span>
      <img src="images/<?= e($values['gambar']) ?>" alt="<?= e($values['nama_des']) ?>">
      <code><?= e($values['gambar']) ?></code>
    </div>
  <?php endif; ?>

  <div class="admin-field">
    <label for="detail_text">Deskripsi destinasi</label>
    <textarea id="detail_text" name="detail_text" rows="10" required><?= e($values['detail_text']) ?></textarea>
    <small>Isi sebagai teks biasa. Sistem akan menyimpan HTML aman untuk halaman detail.</small>
  </div>

  <div class="admin-field">
    <label for="gallery_images">Gambar galeri tambahan</label>
    <input
      id="gallery_images"
      name="gallery_images[]"
      type="file"
      accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
      multiple
    />
    <small>Opsional. Maksimal 4 file per submit, masing-masing 3 MB.</small>
  </div>

  <div class="admin-form__actions">
    <button class="btn btn--primary" type="submit"><?= e($submitLabel) ?></button>
    <a class="btn btn--outline" href="admin/destinations/index.php">Batal</a>
  </div>
</form>
