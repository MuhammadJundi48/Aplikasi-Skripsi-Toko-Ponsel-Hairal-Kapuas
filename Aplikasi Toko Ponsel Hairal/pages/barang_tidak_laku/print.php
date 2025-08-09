
<?php
require_once '../../inc/db.php';
require_once '../../inc/auth.php';
checkRole(['admin','manager','marketing']);

$pageTitle = 'Barang Paling Tidak Laku per Bulan';

function labelize(string $col): string {
  return ucwords(str_replace('_', ' ', $col));
}

$namaBulan = [
  '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
  '04' => 'April',   '05' => 'Mei',      '06' => 'Juni',
  '07' => 'Juli',    '08' => 'Agustus',  '09' => 'September',
  '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
];

// Ambil parameter dari URL
$search = $_GET['search'] ?? '';
$bulan  = $_GET['bulan'] ?? '';
$sort   = $_GET['sort'] ?? 'v.periode_date';
$order  = strtoupper($_GET['order'] ?? 'DESC');

$bulan = str_pad($bulan, 2, '0', STR_PAD_LEFT);

$tahunOffset = isset($_GET['tahun']) && is_numeric($_GET['tahun']) ? (int)$_GET['tahun'] : null;
$tahun = null;
if ($tahunOffset !== null) {
  $tahun = 2025 - $tahunOffset;
}

// Ambil kolom dari VIEW
$raw  = $pdo->query("SHOW COLUMNS FROM vw_barang_tidak_laku")->fetchAll(PDO::FETCH_COLUMN);
$cols = [];
foreach ($raw as $c) $cols[$c] = labelize($c);

// Tambahan kolom dari JOIN
$cols['nama_ponsel']    = 'Nama Ponsel';
$cols['nama_aksesoris'] = 'Nama Aksesoris';

// Validasi sort & order
$allowedSort = array_merge(array_keys($cols), ['v.periode_date', 'p.nama_ponsel', 'a.nama_aksesoris']);
$sort  = in_array($sort, $allowedSort) ? $sort : 'v.periode_date';
$order = in_array($order, ['ASC', 'DESC']) ? $order : 'DESC';

// WHERE CLAUSE
$where = "1";
$params = [];

if ($search !== '') {
  $where .= " AND CONCAT_WS(' ', 
               v.id, v.id_ponsel, p.nama_ponsel,
               v.id_aksesoris, a.nama_aksesoris,
               v.periode
             ) LIKE ?";
  $params[] = "%$search%";
}
if ($bulan && $tahun) {
  $where .= " AND MONTH(v.periode_date) = ? AND YEAR(v.periode_date) = ?";
  $params[] = (int)$bulan;
  $params[] = (int)$tahun;
}

// Query akhir
$sql = "
  SELECT v.*, p.nama_ponsel, a.nama_aksesoris
  FROM vw_barang_tidak_laku v
  LEFT JOIN tb_inventaris_ponsel p ON v.id_ponsel = p.id_ponsel
  LEFT JOIN tb_inventaris_aksesoris a ON v.id_aksesoris = a.id_aksesoris
  WHERE $where
  ORDER BY $sort $order
";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Keterangan periode
$keteranganPeriode = '';
if ($bulan && $tahun && isset($namaBulan[$bulan])) {
  $keteranganPeriode = "Periode: " . $namaBulan[$bulan] . " " . $tahun;
} elseif ($bulan && isset($namaBulan[$bulan])) {
  $keteranganPeriode = "Periode: " . $namaBulan[$bulan];
} elseif ($tahun) {
  $keteranganPeriode = "Tahun: $tahun";
}

$tgl = date('d-m-Y');
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Cetak – <?= htmlspecialchars($pageTitle) ?></title>
  <link href="/bootstrap-5.3.7/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { font-size: 12px; }
    @media print { .no-print { display: none; } }
  </style>
</head>
<body onload="window.print()">

<div class="text-center mb-1">
  <h5 class="mb-0 fw-bold">TOKO PONSEL HAIRAL</h5>
  <small class="d-block">
    Pasar Setia Kawan, Jl. Mawar, Kuala Kapuas, Kalimantan Tengah
  </small>
  <hr class="my-2">
</div>

<h4 class="text-center mb-0"><?= htmlspecialchars($pageTitle) ?></h4>
<?php if ($keteranganPeriode): ?>
  <p class="text-center"><?= htmlspecialchars($keteranganPeriode) ?></p>
<?php endif; ?>

<table class="table table-bordered table-sm mt-3">
  <thead class="table-light"><tr>
    <?php foreach ($cols as $label) echo "<th>$label</th>"; ?>
  </tr></thead>
  <tbody>
    <?php foreach ($data as $row): ?>
    <tr>
      <?php foreach (array_keys($cols) as $col): ?>
        <td><?= htmlspecialchars($row[$col] ?? '') ?></td>
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