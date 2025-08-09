<?php
require_once '../../inc/db.php';
require_once '../../inc/auth.php';
checkRole(['admin','manager']);

$pageTitle = 'Laporan Keuangan';

function labelize(string $col): string {
  return ucwords(str_replace('_', ' ', $col));
}

function namaBulan($angka): string {
  $bulan = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
    4 => 'April', 5 => 'Mei', 6 => 'Juni',
    7 => 'Juli', 8 => 'Agustus', 9 => 'September',
    10 => 'Oktober', 11 => 'November', 12 => 'Desember'
  ];
  return $bulan[(int)$angka] ?? '';
}

function formatRupiah($angka): string {
  return 'Rp ' . number_format((float)$angka, 0, ',', '.');
}

// Ambil kolom dari tabel
$raw  = $pdo->query("SHOW COLUMNS FROM tb_laporan_keuangan")->fetchAll(PDO::FETCH_COLUMN);
$cols = [];
foreach ($raw as $c) $cols[$c] = labelize($c);

// Kolom angka yang diformat rupiah
$formatAngka = ['pendapatan_kotor', 'pengeluaran', 'pendapatan_bersih'];

// Ambil parameter dari URL
$search = $_GET['search'] ?? '';
$sort   = $_GET['sort'] ?? '';
$order  = strtolower($_GET['order'] ?? 'asc');
$bulan  = $_GET['bulan'] ?? '';
$tahunOffset = isset($_GET['tahun']) && is_numeric($_GET['tahun']) ? (int)$_GET['tahun'] : null;
$tahun = null;
if ($tahunOffset !== null) {
  $tahun = 2025 - $tahunOffset;
}

// Filter & where clause
$where = "1";
$params = [];

if ($search !== '') {
  $where .= " AND CONCAT_WS(' ', " . implode(', ', array_keys($cols)) . ") LIKE ?";
  $params[] = "%$search%";
}
if ($bulan !== '') {
  $where .= " AND MONTH(tanggal) = ?";
  $params[] = (int)$bulan;
}
if ($tahun !== null) {
  $where .= " AND YEAR(tanggal) = ?";
  $params[] = (int)$tahun;
}

// Validasi sort dan order
$sort = in_array($sort, array_keys($cols)) ? $sort : 'tanggal';
$order = $order === 'desc' ? 'DESC' : 'ASC';
$orderBy = "ORDER BY $sort $order";

// Query
$sql = "
  SELECT " . implode(', ', array_keys($cols)) . "
  FROM tb_laporan_keuangan
  WHERE $where
  $orderBy
";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Periode teks
$keteranganPeriode = '';
if ($bulan && $tahun) {
  $keteranganPeriode = 'Periode: ' . namaBulan($bulan) . ' ' . $tahun;
} elseif ($bulan) {
  $keteranganPeriode = 'Periode: ' . namaBulan($bulan);
} elseif ($tahun) {
  $keteranganPeriode = 'Tahun: ' . $tahun;
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
  <small class="d-block">Pasar Setia Kawan, Jl. Mawar, Kuala Kapuas, Kalimantan Tengah</small>
  <hr class="my-2">
</div>

<h4 class="text-center mb-0"><?= htmlspecialchars($pageTitle) ?></h4>
<?php if ($keteranganPeriode): ?>
  <p class="text-center"><strong><?= htmlspecialchars($keteranganPeriode) ?></strong></p>
<?php endif; ?>

<table class="table table-bordered table-sm mt-3">
  <thead class="table-light"><tr>
    <?php foreach ($cols as $label): ?>
      <th><?= $label ?></th>
    <?php endforeach; ?>
  </tr></thead>
  <tbody>
    <?php foreach ($data as $row): ?>
    <tr>
      <?php foreach (array_keys($cols) as $col): ?>
        <td class="<?= in_array($col, $formatAngka) ? 'text-end' : '' ?>">
          <?= in_array($col, $formatAngka)
                ? formatRupiah($row[$col])
                : htmlspecialchars($row[$col] ?? '') ?>
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