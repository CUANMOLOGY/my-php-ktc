<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Anggota.php';

$anggota = new Anggota($conn);

// Ambil data anggota yang akan diedit
$id = $_GET['id'] ?? 0;
$data = $anggota->getById($id);

if (!$data) {
    header("Location: list.php?error=Anggota tidak ditemukan");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = htmlspecialchars($_POST['nama']);
    $alamat = htmlspecialchars($_POST['alamat']);
    $telepon = htmlspecialchars($_POST['telepon']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    
    if ($anggota->update($id, $nama, $alamat, $telepon, $email)) {
        header("Location: list.php?success=Data anggota berhasil diperbarui");
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
        <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="POST" class="form">
            <div class="form-group">
                <label for="nama" class="form-label">Nama *</label>
                <input type="text" id="nama" name="nama" class="form-control" 
                       value="<?= $data['nama'] ?>" required>
            </div>
            
            <div class="form-group">
                <label for="alamat" class="form-label">Alamat *</label>
                <textarea id="alamat" name="alamat" class="form-control" rows="3" required><?= 
                    $data['alamat'] ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="telepon" class="form-label">Telepon</label>
                <input type="tel" id="telepon" name="telepon" class="form-control"
                       value="<?= $data['telepon'] ?>">
            </div>
            
            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-control"
                       value="<?= $data['email'] ?>">
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn">Simpan Perubahan</button>
                <a href="list.php" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
    <script src="/perpus/assets/js/anggota.js" defer></script>
</body>
</html>