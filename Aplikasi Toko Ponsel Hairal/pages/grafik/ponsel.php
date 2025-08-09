<?php
/* -------------------------------------------------------------
   Grafik Penjualan Ponsel per Bulan
   Folder : /pages/grafik/   File : ponsel.php
-------------------------------------------------------------- */

require_once '../../inc/db.php';      // koneksi PDO → $pdo
require_once '../../inc/auth.php';    // proteksi login/role
checkRole(['admin','manager','marketing']);

$pageTitle = 'Grafik Penjualan Ponsel';
require_once '../../inc/header.php';

/* ---------- 1) Tentukan kolom tanggal_penjualan ------------ */
$candidates = ['tanggal_penjualan','tanggal','tgl_penjualan','tgl','created_at'];
$dateCol    = null;

foreach ($candidates as $col) {
  $st = $pdo->query("SHOW COLUMNS FROM tb_penjualan_ponsel LIKE '$col'");
  if ($st && $st->rowCount()) {
    $dateCol = $col;
    break;
  }
}

if (!$dateCol) {
  echo '<div class="alert alert-danger">
          Tidak ditemukan kolom tanggal di <b>tb_penjualan_ponsel</b>.<br>
          Tambahkan nama kolom ke array <code>$candidates</code>.
        </div>';
  require_once '../../inc/footer.php'; exit;
}

/* ---------- 2) Ambil jumlah penjualan per bulan ------------ */
$sql = "
    SELECT DATE_FORMAT($dateCol,'%Y-%m') AS bulan,
           COUNT(*)                       AS jumlah
    FROM   tb_penjualan_ponsel
    GROUP  BY bulan
    ORDER  BY bulan
";

$q = $pdo->query($sql);

$labels = $data = [];
foreach ($q as $row) {
  $labels[] = $row['bulan'];
  $data[]   = (int)$row['jumlah'];
}
?>
<h3 class="mb-4">Grafik Penjualan Ponsel per Bulan</h3>
<canvas id="chartPonsel" height="80"></canvas>

<script>
const ctx = document.getElementById('chartPonsel');
new Chart(ctx, {
  type: 'bar',
  data: {
    labels: <?= json_encode($labels) ?>,
    datasets: [{
      label: 'Jumlah Penjualan',
      data: <?= json_encode($data) ?>,
      backgroundColor: 'rgba(13,110,253,0.6)',
      borderColor:   'rgba(13,110,253,1)',
      borderWidth: 1
    }]
  },
  options: {
    scales: { y: { beginAtZero: true } }
  }
});
</script>

<?php require_once '../../inc/footer.php'; ?>