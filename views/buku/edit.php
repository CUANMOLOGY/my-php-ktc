<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Buku.php';

$buku = new Buku($conn);
$data = $buku->getById($_GET['id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($buku->update($_POST['id'], $_POST['judul'], $_POST['pengarang'], $_POST['tahun_terbit'], $_POST['stok'])) {
        header("Location: list.php?sukses=update");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Buku</title>
    <link rel="stylesheet" href="/perpus/assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1 class="page-title">Edit Buku</h1>
        
        <form method="POST" class="form">
            <input type="hidden" name="id" value="<?= $data['id_buku'] ?>">
            
            <div class="form-group">
                <label for="judul">Judul:</label>
                <input type="text" id="judul" name="judul" class="form-control" value="<?= htmlspecialchars($data['judul']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="pengarang">Pengarang:</label>
                <input type="text" id="pengarang" name="pengarang" class="form-control" value="<?= htmlspecialchars($data['pengarang']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="tahun_terbit">Tahun Terbit:</label>
                <input type="number" id="tahun_terbit" name="tahun_terbit" class="form-control" value="<?= htmlspecialchars($data['tahun_terbit']) ?>" required min="1900" max="<?= date('Y') ?>">
            </div>
            
            <div class="form-group">
                <label for="stok">Stok:</label>
                <input type="number" id="stok" name="stok" class="form-control" value="<?= htmlspecialchars($data['stok']) ?>" required min="0">
            </div>
            
            <div class="form-actions">
                <button type="submit" name="update" class="btn">Update</button>
                <a href="list.php" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
</body>
</html>