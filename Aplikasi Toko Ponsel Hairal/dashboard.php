<?php
require_once 'inc/auth.php';
redirectIfNotLoggedIn();
$pageTitle = 'Dashboard';
require_once 'inc/header.php';

/* Notifikasi stok=1 */
$low = $pdo->query("
   SELECT nama_ponsel AS nama,'Ponsel' AS jenis FROM tb_inventaris_ponsel WHERE stok=1
   UNION ALL
   SELECT nama_aksesoris,'Aksesoris' FROM tb_inventaris_aksesoris WHERE stok=1
")->fetchAll();
?>

<?php if ($low): ?>
<div class="alert alert-warning alert-dismissible fade show">
  <strong><i class="bi bi-exclamation-triangle"></i> Stok Hampir Habis:</strong>
  <?php foreach($low as $l): ?>
    <br><?= htmlspecialchars($l['nama']) ?> (<?= $l['jenis'] ?>) tersisa 1.
  <?php endforeach; ?>
  <button class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<h3>Selamat datang!</h3>
<p>Pilih menu di navbar untuk mulai bekerja.</p>

<?php require_once 'inc/footer.php'; ?>