<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Anggota.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $telepon = $_POST['telepon'];
    $email = $_POST['email'];
    
    $anggota = new Anggota($conn);
    if ($anggota->tambah($nama, $alamat, $telepon, $email)) {
        header("Location: list.php?sukses=tambah");
        exit;
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
        
        <form method="POST" class="form">
            <div class="form-group">
                <label for="nama">Nama:</label>
                <input type="text" id="nama" name="nama" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="alamat">Alamat:</label>
                <textarea id="alamat" name="alamat" class="form-control" rows="3"></textarea>
            </div>
            
            <div class="form-group">
                <label for="telepon">Telepon:</label>
                <input type="text" id="telepon" name="telepon" class="form-control">
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" class="form-control">
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn">Simpan</button>
                <a href="list.php" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
</body>
</html>