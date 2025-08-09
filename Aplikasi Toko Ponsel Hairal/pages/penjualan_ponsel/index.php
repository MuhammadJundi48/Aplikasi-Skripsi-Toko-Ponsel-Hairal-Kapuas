<?php
require_once '../../inc/db.php';
require_once '../../inc/auth.php';
checkRole(['admin','manager','kasir']);

$pageTitle     = 'Penjualan Ponsel';
$tableName     = 'tb_penjualan_ponsel';
$allowedRoles  = ['admin','manager','kasir'];
$viewOnly      = false;
$printable     = true;

// Input Tipe & Custom Input
$inputTypes = [
  'tanggal_penjualan' => 'date',
];

$optionsPonsel = $pdo->query("
  SELECT id_ponsel AS id, CONCAT(id_ponsel, ' – ', nama_ponsel) AS label
  FROM tb_inventaris_ponsel
  ORDER BY nama_ponsel
")->fetchAll(PDO::FETCH_ASSOC);

$optionsPelanggan = $pdo->query("
  SELECT id_pelanggan AS id, CONCAT(id_pelanggan, ' – ', nama_pelanggan) AS label
  FROM tb_pelanggan
  ORDER BY nama_pelanggan
")->fetchAll(PDO::FETCH_ASSOC);

$customInput = [
  'tanggal_penjualan' => fn($name, $value = '') =>
    "<input type=\"date\" name=\"$name\" value=\"$value\" class=\"form-control\">",

  'id_ponsel' => function ($name, $value = '') use ($optionsPonsel) {
    $html = "<select name=\"$name\" class=\"form-select\">";
    foreach ($optionsPonsel as $opt) {
      $selected = $value == $opt['id'] ? 'selected' : '';
      $html .= "<option value=\"{$opt['id']}\" $selected>{$opt['label']}</option>";
    }
    $html .= "</select>";
    return $html;
  },

  'id_pelanggan' => function ($name, $value = '') use ($optionsPelanggan) {
    $html = "<select name=\"$name\" class=\"form-select\">";
    foreach ($optionsPelanggan as $opt) {
      $selected = $value == $opt['id'] ? 'selected' : '';
      $html .= "<option value=\"{$opt['id']}\" $selected>{$opt['label']}</option>";
    }
    $html .= "</select>";
    return $html;
  },
];

// Filter Bulan & Tahun
$filterParams = [
  [
    'name' => 'bulan',
    'label' => 'Bulan',
    'type' => 'select',
    'options' => [
      '' => '-- Semua --',
      '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
      '04' => 'April', '05' => 'Mei', '06' => 'Juni',
      '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
      '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
    ],
    'query' => fn($val) => $val ? "MONTH(tanggal_penjualan) = " . intval($val) : '1',
  ],
  [
    'name' => 'tahun',
    'label' => 'Tahun',
    'type' => 'select',
    'options' => array_merge(['' => '-- Semua --'], array_combine(range(date('Y'), 2020), range(date('Y'), 2020))),
    'query' => fn($val) => $val ? "YEAR(tanggal_penjualan) = " . intval($val) : '1',
  ]
];

// Kolom tidak disembunyikan
$hideFields = []; // penting agar id_ponsel & id_pelanggan muncul

// Mapping ID → Nama
$mapPonsel = $pdo->query("SELECT id_ponsel, nama_ponsel FROM tb_inventaris_ponsel")->fetchAll(PDO::FETCH_KEY_PAIR);
$mapPelanggan = $pdo->query("SELECT id_pelanggan, nama_pelanggan FROM tb_pelanggan")->fetchAll(PDO::FETCH_KEY_PAIR);

// Load template utama
require_once '../../inc/page_template.php';
?>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const namaPonsel    = <?= json_encode($mapPonsel) ?>;
  const namaPelanggan = <?= json_encode($mapPelanggan) ?>;

  const tbl = document.querySelector('table.table');
  if (!tbl) return;

  const headers = Array.from(tbl.tHead.rows[0].cells).map(th => th.textContent.trim().toLowerCase());
  const idxPonsel    = headers.findIndex(h => h === 'id ponsel');
  const idxPelanggan = headers.findIndex(h => h === 'id pelanggan');
  const idxAksi      = headers.findIndex(h => h === 'aksi');

  if (!tbl.tHead.querySelector('.th-nama-ponsel')) {
    const th1 = document.createElement('th');
    th1.className = 'th-nama-ponsel';
    th1.textContent = 'Nama Ponsel';
    tbl.tHead.rows[0].insertBefore(th1, tbl.tHead.rows[0].cells[idxAksi]);

    const th2 = document.createElement('th');
    th2.className = 'th-nama-pelanggan';
    th2.textContent = 'Nama Pelanggan';
    tbl.tHead.rows[0].insertBefore(th2, tbl.tHead.rows[0].cells[idxAksi + 1]);
  }

  Array.from(tbl.tBodies[0].rows).forEach(row => {
    const idPonsel = idxPonsel !== -1 ? row.cells[idxPonsel]?.textContent.trim() : '';
    const idPelanggan = idxPelanggan !== -1 ? row.cells[idxPelanggan]?.textContent.trim() : '';

    const td1 = document.createElement('td');
    td1.textContent = namaPonsel[idPonsel] || '-';
    row.insertBefore(td1, row.cells[idxAksi]);

    const td2 = document.createElement('td');
    td2.textContent = namaPelanggan[idPelanggan] || '-';
    row.insertBefore(td2, row.cells[idxAksi]);
  });
});
</script>