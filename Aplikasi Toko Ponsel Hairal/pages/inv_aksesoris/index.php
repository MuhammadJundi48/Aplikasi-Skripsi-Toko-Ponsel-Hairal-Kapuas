<?php
/* ==========================================================
   Modul : Inventaris Aksesoris
   Tabel : tb_inventaris_aksesoris
   Akses : admin, manager
   CRUD  : ya
   Cetak : ya
   ========================================================== */

$pageTitle    = 'Inventaris Aksesoris';
$tableName    = 'tb_inventaris_aksesoris';
$allowedRoles = ['admin','manager'];
$viewOnly     = false;
$printable    = true;

// --- Filter stok (1, 2–4, >5) ---
$filterParams = [
  [
    'label'   => 'Filter Stok',
    'name'    => 'stok',
    'type'    => 'select',
    'options' => [
      ''   => 'Semua',
      '1'  => '1',
      '2'  => '2 - 4',
      '5'  => '> 5',
    ],
    'query'   => function ($value) {
      if ($value === '1') return 'stok = 1';
      if ($value === '2') return 'stok BETWEEN 2 AND 4';
      if ($value === '5') return 'stok > 5';
      return '1'; // tanpa filter
    }
  ]
];

require_once '../../inc/page_template.php';
?>