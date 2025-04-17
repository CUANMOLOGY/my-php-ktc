<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Buku.php';

$buku = new Buku($conn);
$data = $buku->getAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Daftar Buku</title>
    <link rel="stylesheet" href="/perpus/assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="header-actions">
            <h1 class="page-title">Daftar Buku</h1>
            <a href="tambah.php" class="btn">+ Tambah Buku</a>
        </div>

        <?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger">
        <?php
        switch ($_GET['error']) {
            case 'buku_sedang_dipinjam': 
                echo "Buku tidak bisa dihapus karena sedang dipinjam!"; 
                break;
                }
                ?>
                </div>
                <?php endif; ?>

        <?php if (!empty($data)): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Judul</th>
                    <th>Pengarang</th>
                    <th>Tahun Terbit</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id_buku']) ?></td>
                    <td><?= htmlspecialchars($row['judul']) ?></td>
                    <td><?= htmlspecialchars($row['pengarang']) ?></td>
                    <td><?= htmlspecialchars($row['tahun_terbit']) ?></td>
                    <td><?= htmlspecialchars($row['stok']) ?></td>
                    <td class="action-buttons">
                    <a href="hapus.php?id=<?= $row['id_buku'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus buku ini?')">Hapus</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="empty-state">
            <p>Belum ada data buku</p>
        </div>
        <?php endif; ?>

        <div class="navigation-actions">
            <a href="../../index.php" class="btn btn-secondary">Kembali ke Beranda</a>
        </div>
    </div>
</body>
</html>