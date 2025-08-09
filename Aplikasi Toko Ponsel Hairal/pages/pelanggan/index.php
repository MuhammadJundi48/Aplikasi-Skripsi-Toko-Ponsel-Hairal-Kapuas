<?php
$pageTitle    = 'Data Pelanggan';
$tableName    = 'tb_pelanggan';
$allowedRoles = ['admin','manager','marketing','kasir'];

$viewOnly     = false;   // tetap boleh CRUD
$printable    = true;    // ada tombol Cetak

/* ─── Kolom yang TIDAK BOLEH diisi lewat form ─── */
$noInputFields = [
    'jumlah_beli_ponsel',
    'jumlah_beli_aksesoris',
    'total_jumlah_beli'
];

/*  Tidak perlu $hideFields, karena kita ingin kolom
    tetap kelihatan di tabel.                         */

require_once '../../inc/page_template.php';
?>