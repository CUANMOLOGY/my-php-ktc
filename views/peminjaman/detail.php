<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../models/Peminjaman.php';

$peminjaman = new Peminjaman($conn);
$id_peminjaman = $_GET['id'] ?? null;
$detail = $id_peminjaman ? $peminjaman->getDetailPeminjaman($id_peminjaman) : [];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Detail Peminjaman</title>
    <link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">
</head>
<body>
    <div class="container">
        <h1 class="page-title">Detail Peminjaman #<?= $id_peminjaman ?></h1>
        
        <?php if (!empty($detail)): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Buku</th>
                    <th>Pengarang</th>
                    <th>Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($detail as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['judul']) ?></td>
                    <td><?= htmlspecialchars($item['pengarang']) ?></td>
                    <td><?= $item['jumlah'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="alert alert-danger">
            Data peminjaman tidak ditemukan
        </div>
        <?php endif; ?>
        
        <div class="navigation-actions">
            <a href="<?= url('views/peminjaman/list.php') ?>" class="btn btn-secondary">Kembali ke Daftar</a>
        </div>
    </div>
</body>
</html>