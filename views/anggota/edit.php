<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Anggota.php';

$anggota = new Anggota($conn);

// Ambil data anggota yang akan diedit
$id = $_GET['id'] ?? 0;
$data = $anggota->getById($id);

if (!$data) {
    header("Location: list.php?error=notfound");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $telepon = $_POST['telepon'];
    $email = $_POST['email'];
    
    if ($anggota->update($id, $nama, $alamat, $telepon, $email)) {
        header("Location: list.php?sukses=edit");
        exit;
    } else {
        $error = "Gagal memperbarui data anggota";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Anggota</title>
    <link rel="stylesheet" href="/perpus/assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1 class="page-title">Edit Anggota</h1>
        
        <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST" class="form">
            <div class="form-group">
                <label for="nama">Nama:</label>
                <input type="text" id="nama" name="nama" class="form-control" 
                       value="<?= htmlspecialchars($data['nama']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="alamat">Alamat:</label>
                <textarea id="alamat" name="alamat" class="form-control" rows="3" required><?= 
                    htmlspecialchars($data['alamat']) ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="telepon">Telepon:</label>
                <input type="text" id="telepon" name="telepon" class="form-control"
                       value="<?= htmlspecialchars($data['telepon']) ?>">
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" class="form-control"
                       value="<?= htmlspecialchars($data['email']) ?>">
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn">Simpan Perubahan</button>
                <a href="list.php" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
</body>
</html>