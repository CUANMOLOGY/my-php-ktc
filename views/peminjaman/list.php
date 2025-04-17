<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../models/Peminjaman.php';

$peminjaman = new Peminjaman($conn);
// Menggunakan getRiwayatPeminjaman() untuk mendapatkan semua data peminjaman
$data = $peminjaman->getRiwayatPeminjaman();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Daftar Peminjaman Buku</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="header-actions">
            <h1 class="page-title">Daftar Peminjaman Buku</h1>
            <a href="<?= BASE_URL ?>views/peminjaman/pinjam.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Buat Peminjaman Baru
            </a>
        </div>

        <?php if (isset($_GET['sukses'])): ?>
            <div class="alert alert-success">
                <?php
                if ($_GET['sukses'] == 'pinjam') {
                    echo "<i class='fas fa-check-circle'></i> Peminjaman berhasil dicatat!";
                } elseif ($_GET['sukses'] == 'kembali') {
                    $dendaMessage = isset($_GET['denda']) && $_GET['denda'] > 0 ? 
                        " dengan denda Rp " . number_format($_GET['denda'], 0, ',', '.') : '';
                    echo "<i class='fas fa-check-circle'></i> Buku berhasil dikembalikan" . $dendaMessage . "!";
                }
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <?php
                if ($_GET['error'] == 'gagal_kembali') {
                    echo "Gagal mengembalikan buku. Silakan coba lagi.";
                } else {
                    echo htmlspecialchars($_GET['error']);
                }
                ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($data)): ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Anggota</th>
                        <th>Buku yang Dipinjam</th>
                        <th>Tanggal Pinjam</th>
                        <th>Tanggal Kembali</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $row): ?>
                    <tr>
                        <td><?= $row['id_peminjaman'] ?></td>
                        <td><?= htmlspecialchars($row['nama_anggota']) ?></td>
                        <td><?= htmlspecialchars($row['buku']) ?></td>
                        <td><?= date('d-m-Y', strtotime($row['tanggal_pinjam'])) ?></td>
                        <td><?= date('d-m-Y', strtotime($row['tanggal_kembali'])) ?></td>
                        <td>
                            <span class="status-badge <?= 
                                ($row['status_pinjam'] == 'Sudah Kembali') ? 'status-success' : 
                                (($row['status_pinjam'] == 'Terlambat') ? 'status-danger' : 'status-warning')
                            ?>">
                                <?= $row['status_pinjam'] ?>
                                <?php if ($row['status_pinjam'] == 'Terlambat' && $row['denda'] > 0): ?>
                                    <br><small>Denda: Rp <?= number_format($row['denda'], 0, ',', '.') ?></small>
                                <?php endif; ?>
                            </span>
                        </td>
                        <td class="action-buttons">
                            <a href="<?= BASE_URL ?>views/peminjaman/detail.php?id=<?= $row['id_peminjaman'] ?>" 
                               class="btn btn-sm btn-info" title="Detail Peminjaman">
                                <i class="fas fa-eye"></i>
                            </a>
                            <?php if ($row['status_pinjam'] == 'Dipinjam' || $row['status_pinjam'] == 'Terlambat'): ?>
                                <a href="<?= BASE_URL ?>views/peminjaman/kembalikan.php?id=<?= $row['id_peminjaman'] ?>" 
                                   class="btn btn-sm btn-success" 
                                   title="Kembalikan Buku"
                                   onclick="return confirm('Apakah Anda yakin ingin mengembalikan buku ini?')">
                                    <i class="fas fa-book"></i>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-book-open"></i>
            <p>Tidak ada data peminjaman saat ini</p>
        </div>
        <?php endif; ?>
        
        <div class="navigation-actions">
            <a href="<?= BASE_URL ?>views/laporan/peminjaman_anggota.php" class="btn btn-info">
                <i class="fas fa-file-alt"></i> Lihat Laporan
            </a>
            <a href="<?= BASE_URL ?>index.php" class="btn btn-secondary">
                <i class="fas fa-home"></i> Kembali ke Beranda
            </a>
        </div>
    </div>

    <!-- Font Awesome untuk ikon -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    
    <script>
        // Tambahkan konfirmasi sebelum pengembalian
        document.querySelectorAll('a[onclick]').forEach(link => {
            link.addEventListener('click', function(e) {
                if (!confirm(this.getAttribute('data-confirm') || 'Apakah Anda yakin?')) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>