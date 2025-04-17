<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/models/Peminjaman.php';
require_once __DIR__ . '/models/Buku.php';

// Inisialisasi objek
$peminjaman = new Peminjaman($conn);
$buku = new Buku($conn);

// Ambil data statistik
$tahunSekarang = date('Y');
$statistik = $peminjaman->getStatistikPeminjaman($tahunSekarang);
$bukuPopuler = $buku->getBukuPopuler(3);

// Handle error jika ada
if ($statistik === false || $bukuPopuler === false) {
    die("Terjadi kesalahan saat mengambil data. Silakan cek koneksi database dan query.");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Perpustakaan Digital</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
    <link rel="icon" href="<?= BASE_URL ?>assets/images/favicon.ico" type="image/x-icon">
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>Sistem Perpustakaan Digital</h1>
            <p class="subtitle">Manajemen koleksi dan peminjaman buku</p>
        </header>
        
        <nav class="nav-menu">
            <a href="<?= BASE_URL ?>views/anggota/list.php" class="nav-link">
                <i class="fas fa-users"></i> Anggota
            </a>
            <a href="<?= BASE_URL ?>views/buku/list.php" class="nav-link">
                <i class="fas fa-book"></i> Buku
            </a>
            <a href="<?= BASE_URL ?>views/peminjaman/pinjam.php" class="nav-link">
                <i class="fas fa-hand-holding"></i> Peminjaman
            </a>
            <a href="<?= BASE_URL ?>views/peminjaman/list.php" class="nav-link">
                <i class="fas fa-list"></i> Daftar Pinjaman
            </a>
            <a href="<?= BASE_URL ?>views/laporan/buku_populer.php" class="nav-link">
                <i class="fas fa-chart-bar"></i> Laporan
            </a>
        </nav>

        <div class="dashboard-grid">
            <section class="info-box">
                <h3><i class="fas fa-info-circle"></i> Tentang Sistem</h3>
                <p>Sistem ini membantu mengelola:</p>
                <ul class="feature-list">
                    <li>Data anggota perpustakaan</li>
                    <li>Katalog buku dan stok</li>
                    <li>Proses peminjaman dan pengembalian</li>
                    <li>Pelacakan denda keterlambatan</li>
                    <li>Berbagai laporan statistik</li>
                </ul>
            </section>

            <section class="stats-box">
                <h3><i class="fas fa-chart-pie"></i> Statistik Tahun Ini</h3>
                <div class="stat-cards">
                    <div class="stat-card">
                        <i class="fas fa-book-reader"></i>
                        <div>
                            <span class="stat-value"><?= count($statistik) ?></span>
                            <span class="stat-label">Transaksi Bulanan</span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-clock"></i>
                        <div>
                            <span class="stat-value"><?= array_sum(array_column($statistik, 'total_terlambat')) ?></span>
                            <span class="stat-label">Keterlambatan</span>
                        </div>
                    </div>
                </div>
            </section>

            <section class="popular-books">
                <h3><i class="fas fa-star"></i> Buku Populer</h3>
                <div class="book-list">
                    <?php foreach ($bukuPopuler as $buku): ?>
                        <div class="book-item">
                            <div class="book-cover"></div>
                            <div class="book-info">
                                <h4><?= htmlspecialchars($buku['judul']) ?></h4>
                                <p><?= htmlspecialchars($buku['pengarang']) ?></p>
                                <span class="badge"><?= $buku['total_pinjam'] ?> pinjaman</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        </div>

        <footer class="footer">
            <p>&copy; <?= date('Y') ?> Perpustakaan Digital. All rights reserved.</p>
            <p>Versi 1.0.0</p>
        </footer>
    </div>

    <!-- Font Awesome untuk ikon -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>