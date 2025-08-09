<?php
/* -------------------------------------------------------------
   Cetak Data Supplier  —  pages/supplier/print.php
-------------------------------------------------------------- */

require_once '../../inc/db.php'; // koneksi PDO → $pdo
require_once '../../inc/auth.php';
checkRole(['admin','manager']);

$pageTitle = 'Data Supplier';

/* ---------- helper ubah nama_kolom → Nama Kolom ---------- */
function labelize(string $col): string
{
    return ucwords(str_replace('_', ' ', $col));
}

/* ---------- ambil meta kolom ---------- */
$raw  = $pdo->query("SHOW COLUMNS FROM tb_supplier")->fetchAll(PDO::FETCH_COLUMN);
$cols = [];
foreach ($raw as $c) $cols[$c] = labelize($c);

$select = implode(', ', array_keys($cols));

/* ---------- ambil parameter GET ---------- */
$search = $_GET['search'] ?? '';
$sort   = $_GET['sort'] ?? '';
$order  = strtolower($_GET['order'] ?? 'asc');
$order  = in_array($order, ['asc','desc']) ? $order : 'asc';

/* ---------- validasi sort ---------- */
$sort = array_key_exists($sort, $cols) ? $sort : 'id_supplier';

/* ---------- query ---------- */
$sql = "
    SELECT $select
    FROM   tb_supplier
    WHERE  CONCAT_WS(' ', $select) LIKE :search
    ORDER BY $sort $order
";

$stmt = $pdo->prepare($sql);
$stmt->execute(['search' => "%$search%"]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$tgl = date('d-m-Y');
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
  <small class="d-block">
    Pasar Setia Kawan, Jl. Mawar, Kuala Kapuas, Kalimantan Tengah
  </small>
  <hr class="my-2">
</div>

<h4 class="text-center mb-3"><?= htmlspecialchars($pageTitle) ?></h4>

<table class="table table-bordered table-sm">
  <thead class="table-light"><tr>
    <?php foreach ($cols as $lbl) echo "<th>$lbl</th>"; ?>
  </tr></thead>

  <tbody>
    <?php foreach ($data as $r): ?>
      <tr>
        <?php foreach (array_keys($cols) as $f): ?>
          <td><?= htmlspecialchars($r[$f]) ?></td>
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