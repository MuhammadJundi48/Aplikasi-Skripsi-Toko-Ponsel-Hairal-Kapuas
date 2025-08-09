<?php
require_once '../../inc/db.php';
require_once '../../inc/auth.php';
checkRole(['admin','manager','kasir']);

$pageTitle = 'Penjualan Aksesoris';

/* ---------- Helper ---------- */
function labelize(string $col): string {
  return ucwords(str_replace('_', ' ', $col));
}
function formatRupiah($angka): string {
  return number_format($angka, 0, ',', '.');
}
$namaBulan = [
  '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
  '04' => 'April',   '05' => 'Mei',      '06' => 'Juni',
  '07' => 'Juli',    '08' => 'Agustus',  '09' => 'September',
  '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
];

/* ---------- Ambil parameter ---------- */
$search = $_GET['search'] ?? '';
$bulan  = $_GET['bulan']  ?? '';
$sort   = $_GET['sort']   ?? 'id_penjualan_aksesoris';
$order  = $_GET['order']  ?? 'asc';

$bulan = str_pad($bulan, 2, '0', STR_PAD_LEFT); // Normalisasi 2 digit

$tahunOffset = isset($_GET['tahun']) && is_numeric($_GET['tahun']) ? (int)$_GET['tahun'] : null;
$tahun = null;
if ($tahunOffset !== null) {
  $tahun = 2025 - $tahunOffset;
}

/* ---------- Ambil kolom ---------- */
$raw  = $pdo->query("SHOW COLUMNS FROM tb_penjualan_aksesoris")->fetchAll(PDO::FETCH_COLUMN);
$cols = [];
foreach ($raw as $c) $cols[$c] = labelize($c);
$cols['nama_aksesoris'] = 'Nama Aksesoris';
$cols['nama_pelanggan'] = 'Nama Pelanggan';

/* ---------- Validasi sort & order ---------- */
$allowedSort = array_keys($cols);
if (!in_array($sort, $allowedSort)) $sort = 'id_penjualan_aksesoris';
$order = strtolower($order) === 'desc' ? 'DESC' : 'ASC';

/* ---------- Where + Params ---------- */
$where = "1";
$params = [];

if ($search) {
  $where .= " AND CONCAT_WS(' ', pa.id_penjualan_aksesoris, pa.tanggal_penjualan,
                            pa.id_aksesoris, ia.nama_aksesoris,
                            pa.id_pelanggan, pl.nama_pelanggan,
                            pa.harga_terjual) LIKE ?";
  $params[] = "%$search%";
}
if ($bulan && $tahun) {
  $where .= " AND MONTH(pa.tanggal_penjualan) = ? AND YEAR(pa.tanggal_penjualan) = ?";
  $params[] = (int)$bulan;
  $params[] = (int)$tahun;
}

/* ---------- Query ---------- */
$sql = "
  SELECT pa.*, ia.nama_aksesoris, pl.nama_pelanggan
  FROM   tb_penjualan_aksesoris pa
  JOIN   tb_inventaris_aksesoris ia ON pa.id_aksesoris = ia.id_aksesoris
  JOIN   tb_pelanggan pl ON pa.id_pelanggan = pl.id_pelanggan
  WHERE  $where
  ORDER  BY $sort $order
";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ---------- Keterangan ---------- */
$keteranganPeriode = '';
if ($bulan && $tahun && isset($namaBulan[$bulan])) {
  $keteranganPeriode = "Periode: {$namaBulan[$bulan]} $tahun";
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
    body { font-size:12px; }
    @media print { .no-print { display:none } }
  </style>
</head>
<body onload="window.print()">

<!-- KOP -->
<div class="text-center mb-1">
  <h5 class="mb-0 fw-bold">TOKO PONSEL HAIRAL</h5>
  <small class="d-block">Pasar Setia Kawan, Jl. Mawar, Kuala Kapuas, Kalimantan Tengah</small>
  <hr class="my-2">
</div>

<h4 class="text-center mb-0"><?= htmlspecialchars($pageTitle) ?></h4>
<?php if ($keteranganPeriode): ?>
  <p class="text-center"><?= htmlspecialchars($keteranganPeriode) ?></p>
<?php endif; ?>

<!-- TABEL -->
<table class="table table-bordered table-sm mt-3">
  <thead class="table-light">
    <tr><?php foreach ($cols as $label) echo "<th>$label</th>"; ?></tr>
  </thead>
  <tbody>
    <?php foreach ($data as $row): ?>
    <tr>
      <?php foreach (array_keys($cols) as $col): ?>
        <td>
          <?php
            if ($col === 'harga_terjual') {
              echo formatRupiah($row[$col]);
            } else {
              echo htmlspecialchars($row[$col]);
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