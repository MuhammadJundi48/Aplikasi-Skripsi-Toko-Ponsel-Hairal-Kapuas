<?php
require_once '../../inc/db.php';
require_once '../../inc/auth.php';
checkRole(['admin','manager','marketing']);

$pageTitle     = 'Barang Paling Tidak Laku per Bulan';
$tableName     = 'vw_barang_tidak_laku';
$allowedRoles  = ['admin','manager','marketing'];
$viewOnly      = true;
$printable     = true;
$hideFields    = [];
$inputTypes    = [];

// Filter bulan & tahun
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
    'query' => fn($val) => $val ? "MONTH(periode_date) = " . intval($val) : '1',
  ],
  [
    'name' => 'tahun',
    'label' => 'Tahun',
    'type' => 'select',
    'options' => array_merge(['' => '-- Semua --'], array_combine(range(date('Y'), 2020), range(date('Y'), 2020))),
    'query' => fn($val) => $val ? "YEAR(periode_date) = " . intval($val) : '1',
  ]
];

// Kolom tidak disembunyikan
$hideFields = [];

// Mapping ID → Nama
$mapPonsel = $pdo->query("SELECT id_ponsel, nama_ponsel FROM tb_inventaris_ponsel")->fetchAll(PDO::FETCH_KEY_PAIR);
$mapAksesoris = $pdo->query("SELECT id_aksesoris, nama_aksesoris FROM tb_inventaris_aksesoris")->fetchAll(PDO::FETCH_KEY_PAIR);

require_once '../../inc/page_template.php';
?>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const namaPonsel = <?= json_encode($mapPonsel) ?>;
  const namaAksesoris = <?= json_encode($mapAksesoris) ?>;

  const tbl = document.querySelector('table.table');
  if (!tbl) return;

  const headers = Array.from(tbl.tHead.rows[0].cells).map(th => th.textContent.trim().toLowerCase());
  const idxPonsel = headers.findIndex(h => h === 'id ponsel');
  const idxAksesoris = headers.findIndex(h => h === 'id aksesoris');

  if (!tbl.tHead.querySelector('.th-nama-ponsel')) {
    const th1 = document.createElement('th');
    th1.textContent = 'Nama Ponsel';
    th1.className = 'th-nama-ponsel';
    tbl.tHead.rows[0].appendChild(th1);

    const th2 = document.createElement('th');
    th2.textContent = 'Nama Aksesoris';
    th2.className = 'th-nama-aksesoris';
    tbl.tHead.rows[0].appendChild(th2);
  }

  Array.from(tbl.tBodies[0].rows).forEach(row => {
    const idPonsel = idxPonsel !== -1 ? row.cells[idxPonsel]?.textContent.trim() : '';
    const idAksesoris = idxAksesoris !== -1 ? row.cells[idxAksesoris]?.textContent.trim() : '';

    const td1 = document.createElement('td');
    td1.textContent = namaPonsel[idPonsel] || '-';
    row.appendChild(td1);

    const td2 = document.createElement('td');
    td2.textContent = namaAksesoris[idAksesoris] || '-';
    row.appendChild(td2);
  });
});
</script>