<?php
/* ==========================================================
   Modul  : Laporan Keuangan
   Folder : /pages/laporan_keuangan/
   File   : index.php
   Akses  : admin, manager
   ========================================================== */

require_once '../../inc/db.php';
$allowedRoles = ['admin', 'manager'];
require_once '../../inc/auth.php';
checkRole($allowedRoles);

$pageTitle     = 'Laporan Keuangan';
$tableName     = 'tb_laporan_keuangan';
$hideFields    = ['id_laporan_keuangan'];
$readOnly      = ['tanggal', 'pendapatan_kotor', 'pendapatan_bersih'];
$noInputFields = ['pendapatan_kotor', 'pendapatan_bersih'];
$printable     = true;

// Nonaktifkan fungsi tambah dan hapus
$disableAdd    = true;
$disableDelete = true;

$currentMonthYear = date('Y-m'); // contoh: 2025-08

// Proses update pengeluaran, hanya jika bulan = bulan sekarang
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
  $tanggal = $_POST['tanggal'];
  $bulanInput = date('Y-m', strtotime($tanggal));

  if ($bulanInput === $currentMonthYear) {
    $pengeluaran = (float) $_POST['pengeluaran'];

    $stmt = $pdo->prepare("SELECT pendapatan_kotor FROM $tableName WHERE tanggal = ?");
    $stmt->execute([$tanggal]);
    $row = $stmt->fetch();

    if ($row) {
      $pendapatan_bersih = $row['pendapatan_kotor'] - $pengeluaran;
      $stmt = $pdo->prepare("UPDATE $tableName SET pengeluaran = ?, pendapatan_bersih = ? WHERE tanggal = ?");
      $stmt->execute([$pengeluaran, $pendapatan_bersih, $tanggal]);
    }
  }
}

/* ------------------------ Filter Bulan & Tahun -------------------------- */
$filterParams = [  
  [  
    'name'    => 'bulan',  
    'label'   => 'Bulan',  
    'type'    => 'select',  
    'options' => [  
      ''  => '-- Semua --',  
      '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',  
      '04' => 'April', '05' => 'Mei', '06' => 'Juni',  
      '07' => 'Juli', '08' => 'Agustus', '09' => 'September',  
      '10' => 'Oktober', '11' => 'November', '12' => 'Desember'  
    ],  
    'query' => function($val) {  
      return $val ? "MONTH(tanggal) = " . intval($val) : '1';  
    }  
  ],  
  [  
    'name'    => 'tahun',  
    'label'   => 'Tahun',  
    'type'    => 'select',  
    'options' => array_merge(['' => '-- Semua --'], array_combine(range(date('Y'), 2020), range(date('Y'), 2020))),  
    'query' => function($val) {  
      return $val ? "YEAR(tanggal) = " . intval($val) : '1';  
    }  
  ]  
];

// Kirim variabel $currentMonthYear ke template agar bisa sembunyikan tombol update
$GLOBALS['customVars']['currentMonthYear'] = $currentMonthYear;

$GLOBALS['customOrder'] = 'tanggal DESC';

require_once '../../inc/page_template.php';