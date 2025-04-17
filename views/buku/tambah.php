<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Buku.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $buku = new Buku($conn);
    if ($buku->tambah($_POST['judul'], $_POST['pengarang'], $_POST['tahun_terbit'], $_POST['stok'])) {
        header("Location: list.php?sukses=tambah");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Buku</title>
    <link rel="stylesheet" href="/perpus/assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1 class="page-title">Tambah Buku Baru</h1>
        
        <form method="POST" class="form">
            <div class="form-group">
                <label for="judul">Judul:</label>
                <input type="text" id="judul" name="judul" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="pengarang">Pengarang:</label>
                <input type="text" id="pengarang" name="pengarang" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="tahun_terbit">Tahun Terbit:</label>
                <input type="number" id="tahun_terbit" name="tahun_terbit" class="form-control" required min="1900" max="<?= date('Y') ?>">
            </div>
            
            <div class="form-group">
                <label for="stok">Stok:</label>
                <input type="number" id="stok" name="stok" class="form-control" required min="0">
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn">Simpan</button>
                <a href="list.php" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
</body>
</html>