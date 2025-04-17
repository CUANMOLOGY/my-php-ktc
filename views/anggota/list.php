<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Anggota.php';

$anggota = new Anggota($conn);
$data = $anggota->getAll();
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
                <tr>
                    <td><?= htmlspecialchars($row['id_anggota']) ?></td>
                    <td><?= htmlspecialchars($row['nama']) ?></td>
                    <td><?= htmlspecialchars($row['alamat']) ?></td>
                    <td><?= htmlspecialchars($row['telepon']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td class="action-buttons">
                        <a href="edit.php?id=<?= $row['id_anggota'] ?>" class="btn btn-sm">Edit</a>
                        <a href="hapus.php?id=<?= $row['id_anggota'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
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
</body>
</html>