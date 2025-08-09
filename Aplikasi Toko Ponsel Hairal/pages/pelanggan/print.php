<?php
/* -------------------------------------------------------------
   Cetak Data Pelanggan – pages/pelanggan/print.php
-------------------------------------------------------------- */

require_once '../../inc/db.php';
require_once '../../inc/auth.php';
checkRole(['admin','manager','marketing','kasir']);

$pageTitle = 'Data Pelanggan';

/* ---------- helper ubah nama_kolom → Nama Kolom ---------- */
function labelize(string $col): string {
    return ucwords(str_replace('_', ' ', $col));
}

/* ---------- ambil meta kolom ---------- */
$raw  = $pdo->query("SHOW COLUMNS FROM tb_pelanggan")->fetchAll(PDO::FETCH_COLUMN);
$cols = [];
foreach ($raw as $c) $cols[$c] = labelize($c);
$colFields = array_keys($cols);

/* ---------- ambil parameter dari URL ---------- */
$search = $_GET['search'] ?? '';
$sort   = $_GET['sort']   ?? $colFields[0]; // default ke kolom pertama
$order  = strtolower($_GET['order'] ?? 'asc');

/* ---------- validasi sort dan order ---------- */
if (!in_array($sort, $colFields)) {
  $sort = $colFields[0];
}
if (!in_array($order, ['asc', 'desc'])) {
  $order = 'asc';
}

$select = implode(', ', $colFields);
$collate = 'utf8mb4_general_ci';

/* ---------- ambil data sesuai filter ---------- */
$whereSql = "CONCAT_WS(' ', $select) COLLATE $collate LIKE CONVERT(:s USING utf8mb4) COLLATE $collate";
$sql = "SELECT $select FROM tb_pelanggan WHERE $whereSql ORDER BY `$sort` $order";

$stmt = $pdo->prepare($sql);
$stmt->execute(['s' => '%'.$search.'%']);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$tgl  = date('d-m-Y');
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Cetak – <?= htmlspecialchars($pageTitle) ?></title>
  <link href="/bootstrap-5.3.7/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { font-size:12px; }
    @media print { .no-print { display:none } }
  </style>
</head>
<body onload="window.print()">

<!-- ────────── KOP TOKO ────────── -->
<div class="text-center mb-1">
  <h5 class="mb-0 fw-bold">TOKO PONSEL HAIRAL</h5>
  <small class="d-block">Pasar Setia Kawan, Jl. Mawar, Kuala Kapuas, Kalimantan Tengah</small>
  <hr class="my-2">
</div>

<h4 class="text-center mb-3"><?= htmlspecialchars($pageTitle) ?></h4>

<table class="table table-bordered table-sm">
  <thead class="table-light">
    <tr>
      <?php foreach ($cols as $label): ?>
        <th><?= htmlspecialchars($label) ?></th>
      <?php endforeach; ?>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($data as $row): ?>
      <tr>
        <?php foreach ($colFields as $f): ?>
          <td><?= htmlspecialchars($row[$f]) ?></td>
        <?php endforeach; ?>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php if (!$data): ?>
  <p class="text-center">Tidak ada data.</p>
<?php endif; ?>

<br><br><br>
<div style="width:240px;float:right;text-align:center;">
  Kuala Kapuas, <?= $tgl ?><br>
  Mengetahui,<br><br><br><br>
  <u>Suryawan</u><br>
  Pemilik Toko Ponsel Hairal
</div>
</body>
</html>