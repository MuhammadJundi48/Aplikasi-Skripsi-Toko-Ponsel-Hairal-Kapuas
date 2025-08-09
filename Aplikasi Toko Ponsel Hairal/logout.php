<?php
/* logout.php  ----------------------------------- */

if (session_status() === PHP_SESSION_NONE) {
    session_start();                      // pastikan session aktif
}

$_SESSION = [];                           // hapus data sesi
session_destroy();                        // hancurkan file sesi

header('Location: login.php');            // kembali ke halaman login
exit;
