<?php
require_once __DIR__ . '/../models/Peminjaman.php';
require_once __DIR__ . '/../models/Anggota.php';
require_once __DIR__ . '/../models/Buku.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';

$peminjaman = new Peminjaman($conn);
$anggota = new Anggota($conn);
$buku = new Buku($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validasi input
        if (empty($_POST['id_anggota']) || empty($_POST['id_buku']) || empty($_POST['jumlah']) || empty($_POST['tanggal_kembali'])) {
            throw new Exception("Semua field harus diisi");
        }

        $result = $peminjaman->pinjam(
            $_POST['id_anggota'],
            $_POST['id_buku'],
            $_POST['jumlah'],
            $_POST['tanggal_kembali']
        );
        
        if ($result === true) {
            header("Location: " . url('views/peminjaman/list.php?sukses=pinjam'));
        } else {
            header("Location: " . url('views/peminjaman/pinjam.php?error=' . $result));
        }
    } catch (Exception $e) {
        error_log("Error peminjaman: " . $e->getMessage());
        header("Location: " . url('views/peminjaman/pinjam.php?error=' . urlencode($e->getMessage())));
    }
    exit;
}

if (isset($_GET['kembalikan'])) {
    try {
        $success = $peminjaman->kembalikan($_GET['kembalikan']);
        header("Location: " . url('views/peminjaman/list.php?' . ($success ? 'sukses=kembali' : 'error=gagal_kembali')));
    } catch (Exception $e) {
        error_log("Error pengembalian: " . $e->getMessage());
        header("Location: " . url('views/peminjaman/list.php?error=' . urlencode($e->getMessage())));
    }
    exit;
}
?>