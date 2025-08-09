<?php
/* ---------------------------------------------------------
   header.php  –  Template pembuka + Navbar dinamis
---------------------------------------------------------- */
if (!isset($pageTitle)) $pageTitle = 'Dashboard';

if (session_status() === PHP_SESSION_NONE) session_start();

/* ===== helper izin sederhana ===== */
$role = $_SESSION['user']['role'] ?? '';
function canAccess(array $roles): bool
{
    global $role;
    return $role === 'admin' || in_array($role, $roles, true);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= htmlspecialchars($pageTitle) ?> | Aplikasi Toko Ponsel Hairal</title>

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link href="/bootstrap-5.3.7/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="/bootstrap-5.3.7/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="/bootstrap-5.3.7/assets/css/custom.css" rel="stylesheet">
  <script src="/bootstrap-5.3.7/assets/vendor/chart.js/chart.umd.min.js"></script>
  <script src="/bootstrap-5.3.7/assets/vendor/chart.js/chart.umd.js"></script>
</head>

<body class="bg-white text-dark" style="font-family:'Poppins',sans-serif;">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
  <div class="container">
    <a class="navbar-brand fw-bold" href="/bootstrap-5.3.7/dashboard.php">📱 Toko Ponsel Hairal</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav me-auto">
        <!-- Dashboard -->
        <li class="nav-item">
          <a class="nav-link" href="/bootstrap-5.3.7/dashboard.php">
            <i class="bi bi-speedometer2"></i> Dashboard
          </a>
        </li>

        <!-- Data Master -->
        <?php if (canAccess(['manager','marketing','kasir'])): ?>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#"><i class="bi bi-folder"></i> Data Master</a>
          <ul class="dropdown-menu">
            <?php if (canAccess(['admin'])): ?>
              <li><a class="dropdown-item" href="/bootstrap-5.3.7/pages/pengguna/"><i class="bi bi-person-badge"></i> Pengguna</a></li>
            <?php endif; ?>
            <?php if (canAccess(['admin','manager','marketing','kasir'])): ?>
              <li><a class="dropdown-item" href="/bootstrap-5.3.7/pages/pelanggan/">Pelanggan</a></li>
            <?php endif; ?>
            <?php if (canAccess(['admin','manager'])): ?>
              <li><a class="dropdown-item" href="/bootstrap-5.3.7/pages/supplier/">Supplier</a></li>
            <?php endif; ?>
            <?php if (canAccess(['admin'])): ?>
              <li><a class="dropdown-item" href="/bootstrap-5.3.7/pages/staf/">Biodata Staf</a></li>
            <?php endif; ?>
          </ul>
        </li>
        <?php endif; ?>

        <!-- Data Transaksi -->
        <?php if (canAccess(['manager','marketing','kasir'])): ?>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#"><i class="bi bi-basket"></i> Data Transaksi</a>
          <ul class="dropdown-menu">
            <?php if (canAccess(['admin','manager'])): ?>
              <li><a class="dropdown-item" href="/bootstrap-5.3.7/pages/inv_ponsel/">Inventaris Ponsel</a></li>
              <li><a class="dropdown-item" href="/bootstrap-5.3.7/pages/inv_aksesoris/">Inventaris Aksesoris</a></li>
              <li><a class="dropdown-item" href="/bootstrap-5.3.7/pages/laporan_keuangan/">Laporan Keuangan</a></li>
            <?php endif; ?>

            <?php if (canAccess(['admin','manager','kasir'])): ?>
              <li><a class="dropdown-item" href="/bootstrap-5.3.7/pages/penjualan_ponsel/">Penjualan Ponsel</a></li>
              <li><a class="dropdown-item" href="/bootstrap-5.3.7/pages/penjualan_aks/">Penjualan Aksesoris</a></li>
            <?php endif; ?>

            <?php if (canAccess(['admin','manager','marketing'])): ?>
              <li><a class="dropdown-item" href="/bootstrap-5.3.7/pages/barang_terlaris/">Barang Terlaris</a></li>
              <li><a class="dropdown-item" href="/bootstrap-5.3.7/pages/barang_tidak_laku/">Barang Tidak Laku</a></li>
            <?php endif; ?>
          </ul>
        </li>
        <?php endif; ?>

        <!-- Grafik -->
        <?php if (canAccess(['admin','manager','marketing'])): ?>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#"><i class="bi bi-bar-chart"></i> Grafik</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="/bootstrap-5.3.7/pages/grafik/ponsel.php">Penjualan Ponsel</a></li>
            <li><a class="dropdown-item" href="/bootstrap-5.3.7/pages/grafik/aksesoris.php">Penjualan Aksesoris</a></li>
            <li><a class="dropdown-item" href="/bootstrap-5.3.7/pages/grafik/keuangan.php">Keuangan</a></li>
          </ul>
        </li>
        <?php endif; ?>
      </ul>

      <!-- User & Logout -->
      <span class="navbar-text me-3">
        <?= htmlspecialchars($_SESSION['user']['username'] ?? '') ?>
        (<?= htmlspecialchars($role) ?>)
      </span>
      <a class="btn btn-light btn-sm" href="/bootstrap-5.3.7/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </div>
  </div>
</nav>

<div class="container">