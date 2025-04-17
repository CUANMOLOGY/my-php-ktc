<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Anggota.php';

$anggota = new Anggota($conn);
$data = $anggota->getAll();

$success_message = '';
$error_message = '';

if (isset($_GET['success'])) {
    $success_message = match($_GET['success']) {
        'tambah' => 'Anggota baru berhasil ditambahkan',
        'edit' => 'Data anggota berhasil diperbarui',
        'hapus' => 'Anggota berhasil dihapus',
        default => 'Operasi berhasil'
    };
}

if (isset($_GET['error'])) {
    $error_message = match($_GET['error']) {
        'notfound' => 'Anggota tidak ditemukan',
        'hapus' => 'Gagal menghapus anggota',
        default => 'Terjadi kesalahan'
    };
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Daftar Anggota</title>
    <link rel="stylesheet" href="/perpus/assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1 class="page-title">Daftar Anggota</h1>
        
        <?php if ($success_message): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($success_message) ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error_message): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>

        <a href="tambah.php" class="btn mb-20">+ Tambah Anggota</a>
        
        <?php if (!empty($data)): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Alamat</th>
                    <th>Telepon</th>
                    <th>Email</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $row): ?>
                <tr data-id="<?= $row['id_anggota'] ?>">
                    <td><?= htmlspecialchars($row['id_anggota'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($row['nama'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($row['alamat'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($row['telepon'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($row['email'] ?? '-') ?></td>
                    <td class="action-buttons">
                        <a href="edit.php?id=<?= $row['id_anggota'] ?>" class="btn btn-sm">Edit</a>
                        <button class="btn btn-sm btn-danger delete-btn">Hapus</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="empty-state">
            <p>Belum ada data anggota</p>
        </div>
        <?php endif; ?>
        
        <div class="navigation-actions">
            <a href="../../index.php" class="btn">Kembali ke Beranda</a>
        </div>
    </div>
    <script src="/perpus/assets/js/anggota.js" defer></script>
</body>
</html>