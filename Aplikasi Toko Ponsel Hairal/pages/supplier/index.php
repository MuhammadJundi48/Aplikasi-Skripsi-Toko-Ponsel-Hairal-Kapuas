<?php
/* ==========================================================
   Modul : Data Supplier
   Tabel : tb_supplier
   Akses : admin, manager
   CRUD  : ya
   Cetak : ya
   ========================================================== */

$pageTitle    = 'Data Supplier';
$tableName    = 'tb_supplier';
$allowedRoles = ['admin','manager'];
$viewOnly     = false;
$printable    = true;

require_once '../../inc/page_template.php';
?>
