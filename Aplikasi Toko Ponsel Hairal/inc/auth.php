<?php
/**
 * inc/auth.php
 * -------------------------------------------------
 *  • Memulai session dengan aman (tidak ganda)
 *  • Fungsi helper otentikasi & otorisasi
 *  • Meng-include konfigurasi DB
 * -------------------------------------------------
 */

/* 1️⃣  Pastikan session aktif sebelum output apa pun */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* 2️⃣  Koneksi & konstanta lain (tidak boleh ada output) */
require_once __DIR__ . '/config.php';

/* ───────────── Helper ───────────── */

/**
 * Apakah user sudah login?
 */
function isLoggedIn(): bool
{
    return isset($_SESSION['user']);
}

/**
 * Redirect ke halaman login jika belum login
 */
function redirectIfNotLoggedIn(): void
{
    if (!isLoggedIn()) {
        header('Location: /bootstrap-5.3.7/login.php');
        exit;
    }
}

/**
 * Cek role; jika tidak termasuk yang diizinkan → 403
 *
 * @param array $allowed  Daftar role yang diizinkan (['admin','manager',...])
 */
function checkRole(array $allowed): void
{
    $role = $_SESSION['user']['role'] ?? '';
    if (!in_array($role, $allowed, true)) {
        http_response_code(403);
        exit('<h2 style="text-align:center">403 | Access Denied</h2>');
    }
}

/* -------------------------------------------------
   Penggunaan di halaman:
     require_once '../../inc/auth.php';
     redirectIfNotLoggedIn();
     checkRole(['admin','manager']);
   ------------------------------------------------- */
