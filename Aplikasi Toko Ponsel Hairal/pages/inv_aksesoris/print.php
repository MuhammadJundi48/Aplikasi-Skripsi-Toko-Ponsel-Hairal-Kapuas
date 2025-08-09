<?php
/* -------------------------------------------------------------
   Cetak Inventaris Aksesoris – pages/inv_aksesoris/print.php
-------------------------------------------------------------- */

require_once '../../inc/db.php';     // koneksi PDO → $pdo
require_once '../../inc/auth.php';   // cek login & role
checkRole(['admin','manager']);

$pageTitle = 'Inventaris Aksesoris';

/* ---------- helper ubah nama_kolom → Nama Kolom ---------- */
function labelize(string $col): string {
  return ucwords(str_replace('_', ' ', $col));
}

/* ---------- helper format angka ---------- */
function formatRupiah($angka): string {
  return number_format($angka, 0, ',', '.');
}

/* ---------- ambil kolom ---------- */
$raw  = $pdo->query("SHOW COLUMNS FROM tb_inventaris_aksesoris")->fetchAll(PDO::FETCH_COLUMN);
$cols = [];
foreach ($raw as $c) $cols[$c] = labelize($c);
$select = implode(', ', array_keys($cols));

/* ---------- ambil parameter ---------- */
$search = $_GET['search'] ?? '';
$sort   = $_GET['sort']   ?? '';
$order  = $_GET['order']  ?? 'asc';
$filter = $_GET['stok'] ?? '';

/* ---------- where stok filter ---------- */
$where = "1";
$params = [];

if ($search) {
  $where .= " AND CONCAT_WS(' ', $select) LIKE ?";
  $params[] = '%' . $search . '%';
}

if ($filter === '1') {
  $where .= " AND stok = 1";
} elseif ($filter === '2') {
  $where .= " AND stok BETWEEN 2 AND 4";
} elseif ($filter === '5') {
  $where .= " AND stok > 5";
}

/* ---------- validasi sort ---------- */
$sort = in_array($sort, array_keys($cols)) ? $sort : 'id';
$order = strtolower($order) === 'desc' ? 'DESC' : 'ASC';

/* ---------- eksekusi query ---------- */
$stmt = $pdo->prepare("
  SELECT $select
  FROM   tb_inventaris_aksesoris
  WHERE  $where
  ORDER  BY $sort $order
");
$stmt->execute($params);
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

<?php
$keterangan = '';
if ($filter === '1')      $keterangan = 'Stok = 1';
elseif ($filter === '2')  $keterangan = 'Stok 2 – 4';
elseif ($filter === '5')  $keterangan = 'Stok > 5';
?>
<?php if ($keterangan): ?>
  <p class="text-center">Filter: <strong><?= $keterangan ?></strong></p>
<?php endif; ?>

<table class="table table-bordered table-sm">
  <thead class="table-light">
    <tr><?php foreach ($cols as $lbl) echo "<th>$lbl</th>"; ?></tr>
  </thead>
  <tbody>
    <?php foreach ($data as $r): ?>
      <tr>
        <?php foreach (array_keys($cols) as $f): ?>
          <td>
            <?php
              if ($f === 'harga_modal') {
                echo formatRupiah($r[$f]);
              } else {
                echo htmlspecialchars($r[$f]);
              }
            ?>
          </td>
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