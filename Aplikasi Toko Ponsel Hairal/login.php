<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'inc/config.php';

if (isset($_SESSION['user'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $st = $pdo->prepare("SELECT * FROM tb_pengguna WHERE username = ?");
    $st->execute([$username]);
    $u  = $st->fetch(PDO::FETCH_ASSOC);

    if ($u && password_verify($password, $u['password'])) {
        $_SESSION['user'] = [
            'id'       => $u['id_pengguna'],
            'username' => $u['username'],
            'role'     => $u['role']
        ];
        header('Location: dashboard.php');
        exit;
    }
    $error = 'Username atau password salah';
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Login – Toko Ponsel Hairal</title>

  <link href="/bootstrap-5.3.7/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="/bootstrap-5.3.7/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="/bootstrap-5.3.7/assets/css/custom.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .card-header img {
      max-height: 100px;
      object-fit: contain;
      margin-top: 10px;
    }
  </style>
</head>

<body>

<div class="container-fluid">
  <div class="row min-vh-100 align-items-center justify-content-center">
    <div class="col-sm-10 col-md-6 col-lg-4">
      <div class="card shadow">
        <div class="card-header bg-primary text-white text-center">
          <h5 class="mb-2">Aplikasi Toko Ponsel Hairal</h5>
<img src="assets/pictures/LogoPonselHairal.jpg"
     alt="Logo Ponsel Hairal"
     class="rounded-circle mx-auto d-block shadow"
     style="width: 150px; height: 150px; object-fit: cover; border: 4px solid white;">
        </div>
        <div class="card-body">
          <?php if ($error): ?>
            <div class="alert alert-danger small"><?= htmlspecialchars($error) ?></div>
          <?php endif; ?>

          <form method="post" autocomplete="off">
            <div class="mb-3">
              <label class="form-label">Username</label>
              <input type="text" name="username" class="form-control" required autofocus>
            </div>
            <div class="mb-3">
              <label class="form-label">Password</label>
              <input type="password" name="password" class="form-control" required>
            </div>
            <button class="btn btn-primary w-100">
              <i class="bi bi-box-arrow-in-right"></i> Masuk
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="/bootstrap-5.3.7/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>