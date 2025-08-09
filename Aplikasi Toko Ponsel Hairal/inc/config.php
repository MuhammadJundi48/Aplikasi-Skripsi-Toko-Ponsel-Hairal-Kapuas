<?php
/*  inc/config.php
    ------------------------------------------------------------------
    • Membuat objek PDO $pdo untuk database MySQL
    • Tidak memulai session di sini
    • Tanpa tag penutup ?>  → mencegah whitespace tak sengaja
    ------------------------------------------------------------------ */

$host     = 'localhost';
$db       = 'db_ponsel';
$user     = 'root';
$pass     = '';
$charset  = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    exit('Koneksi gagal: ' . $e->getMessage());
}

/*  Catatan:
    - Session kini ditangani di inc/auth.php (atau login.php) supaya tidak double.
    - Jangan tambahkan tag penutup `?>` agar tidak ada output tak sengaja. */
