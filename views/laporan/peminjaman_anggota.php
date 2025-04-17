<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../models/Anggota.php';

$anggota = new Anggota($conn);
$laporan = $anggota->getLaporanPeminjaman();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Laporan Peminjaman per Anggota</title>
    <link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">
</head>
<body>
    <div class="container">
        <h1 class="page-title">Laporan Peminjaman per Anggota</h1>
        
        <div class="filter-section">
            <form method="get" class="form-inline">
                <div class="form-group">
                    <label for="tahun">Tahun:</label>
                    <select id="tahun" name="tahun" class="form-control">
                        <option value="">Semua Tahun</option>
                        <?php for ($i = date('Y'); $i >= 2020; $i--): ?>
                            <option value="<?= $i ?>" <?= ($_GET['tahun'] ?? '') == $i ? 'selected' : '' ?>>
                                <?= $i ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-filter">Filter</button>
                <a href="<?= url('views/laporan/cetak_peminjaman_anggota.php?' . http_build_query($_GET)) ?>" 
                   class="btn btn-primary" 
                   target="_blank">Cetak Laporan</a>
            </form>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Anggota</th>
                    <th>Jumlah Pinjaman</th>
                    <th>Buku Favorit</th>
                    <th>Rata-rata Durasi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($laporan as $index => $row): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= htmlspecialchars($row['nama']) ?></td>
                    <td><?= $row['total_pinjam'] ?> kali</td>
                    <td><?= htmlspecialchars($row['buku_favorit']) ?></td>
                    <td><?= $row['rata_durasi'] ?> hari</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="navigation-actions">
            <a href="<?= url('views/peminjaman/list.php') ?>" class="btn btn-secondary">Kembali ke Daftar Peminjaman</a>
        </div>
    </div>
</body>
</html>