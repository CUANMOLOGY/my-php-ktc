<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Anggota.php';

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = htmlspecialchars($_POST['nama']);
    $alamat = htmlspecialchars($_POST['alamat']);
    $telepon = htmlspecialchars($_POST['telepon']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    
    $anggota = new Anggota($conn);
    if ($anggota->tambah($nama, $alamat, $telepon, $email)) {
        header("Location: list.php?success=Anggota baru berhasil ditambahkan");
        exit;
    } else {
        $error = "Gagal menambahkan anggota baru";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Anggota</title>
    <link rel="stylesheet" href="/perpus/assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1 class="page-title">Tambah Anggota Baru</h1>
        
        <?php if ($error): ?>
        <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="POST" class="form">
            <div class="form-group">
                <label for="nama" class="form-label">Nama *</label>
                <input type="text" id="nama" name="nama" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="alamat" class="form-label">Alamat *</label>
                <textarea id="alamat" name="alamat" class="form-control" rows="3" required></textarea>
            </div>
            
            <div class="form-group">
                <label for="telepon" class="form-label">Telepon</label>
                <input type="tel" id="telepon" name="telepon" class="form-control">
            </div>
            
            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-control">
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn">Simpan</button>
                <a href="list.php" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
    <script src="/perpus/assets/js/anggota.js" defer></script>
</body>
</html>