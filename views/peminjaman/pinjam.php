<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../models/Anggota.php';
require_once __DIR__ . '/../../models/Buku.php';
require_once __DIR__ . '/../../models/Peminjaman.php';

$anggota = new Anggota($conn);
$buku = new Buku($conn);
$peminjaman = new Peminjaman($conn);

$daftarAnggota = $anggota->getAllActive();
$daftarBuku = $buku->getAvailable();

// Definisikan kode error jika belum ada
if (!defined('ERROR_STOK_TIDAK_CUKUP')) {
    define('ERROR_STOK_TIDAK_CUKUP', 1);
    define('ERROR_ANGGOTA_BLOCKED', 2);
    define('ERROR_BUKU_TIDAK_ADA', 3);
    define('ERROR_ANGGOTA_TIDAK_ADA', 4);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Peminjaman Buku</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1 class="page-title">Form Peminjaman Buku</h1>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($_GET['error']) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_URL ?>controllers/peminjaman_controller.php" class="form" id="formPinjam">
            <div class="form-group">
                <label for="id_anggota">Anggota:</label>
                <select id="id_anggota" name="id_anggota" class="form-control" required>
                    <option value="">Pilih Anggota</option>
                    <?php foreach ($daftarAnggota as $anggota): ?>
                        <option value="<?= $anggota['id_anggota'] ?>">
                            <?= htmlspecialchars($anggota['nama']) ?> (ID: <?= $anggota['id_anggota'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="id_buku">Buku:</label>
                <select id="id_buku" name="id_buku" class="form-control" required>
                    <option value="">Pilih Buku</option>
                    <?php foreach ($daftarBuku as $buku): ?>
                        <option value="<?= $buku['id_buku'] ?>" data-stok="<?= $buku['stok'] ?>">
                            <?= htmlspecialchars($buku['judul']) ?> - Stok: <?= $buku['stok'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="jumlah">Jumlah:</label>
                <input type="number" id="jumlah" name="jumlah" class="form-control" min="1" value="1" required>
                <small id="stok-info" class="text-muted"></small>
            </div>
            
            <div class="form-group">
                <label for="tanggal_kembali">Tanggal Kembali:</label>
                <input type="date" id="tanggal_kembali" name="tanggal_kembali" class="form-control" required 
                       min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Proses Peminjaman</button>
                <a href="<?= BASE_URL ?>index.php" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>

    <script>
        // Validasi stok buku
        document.getElementById('id_buku').addEventListener('change', function() {
            updateStokInfo();
        });
        
        document.getElementById('jumlah').addEventListener('input', function() {
            updateStokInfo();
        });
        
        function updateStokInfo() {
            const bukuSelect = document.getElementById('id_buku');
            const jumlahInput = document.getElementById('jumlah');
            const stokInfo = document.getElementById('stok-info');
            
            if (bukuSelect.value) {
                const stok = parseInt(bukuSelect.options[bukuSelect.selectedIndex].getAttribute('data-stok'));
                const jumlah = parseInt(jumlahInput.value) || 0;
                
                stokInfo.textContent = `Stok tersedia: ${stok}`;
                jumlahInput.max = stok;
                
                if (jumlah > stok) {
                    stokInfo.style.color = 'red';
                    stokInfo.textContent += ' (Jumlah melebihi stok!)';
                } else {
                    stokInfo.style.color = 'green';
                }
            }
        }
        
        // Validasi sebelum submit
        document.getElementById('formPinjam').addEventListener('submit', function(e) {
            const bukuSelect = document.getElementById('id_buku');
            const jumlahInput = document.getElementById('jumlah');
            const stok = parseInt(bukuSelect.options[bukuSelect.selectedIndex].getAttribute('data-stok'));
            
            if (parseInt(jumlahInput.value) > stok) {
                e.preventDefault();
                alert('Jumlah peminjaman melebihi stok yang tersedia!');
                return false;
            }
            
            // Validasi tanggal kembali
            const today = new Date();
            const tglKembali = new Date(document.getElementById('tanggal_kembali').value);
            
            if (tglKembali <= today) {
                e.preventDefault();
                alert('Tanggal kembali harus setelah hari ini');
                return false;
            }
            
            return true;
        });
        
        // Inisialisasi awal
        updateStokInfo();
    </script>
</body>
</html>