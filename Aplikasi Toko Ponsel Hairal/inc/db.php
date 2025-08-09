<?php
/*  inc/db.php  ---------------------------------------------------------- */
/*  Ubah user, password, dan host sesuai XAMPP Anda                        */
try {
    $pdo = new PDO(
        'mysql:host=localhost;dbname=db_ponsel;charset=utf8mb4',
        'root',         // ← user MySQL
        '',             // ← password MySQL
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    exit('Koneksi DB gagal: ' . $e->getMessage());
}