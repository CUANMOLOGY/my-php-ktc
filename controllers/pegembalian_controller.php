<?php
require_once __DIR__ . '/../models/Peminjaman.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';

// Inisialisasi koneksi dan controller
$peminjaman = new Peminjaman($conn);

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['kembalikan'])) {
    $result = $peminjaman->kembalikan($_GET['kembalikan']);
    
    if ($result['success']) {
        $urlRedirect = BASE_URL . 'views/peminjaman/list.php?sukses=kembali';
        if ($result['denda'] > 0) {
            $urlRedirect .= '&denda=' . $result['denda'];
        }
        header("Location: " . $urlRedirect);
    } else {
        header("Location: " . BASE_URL . 'views/peminjaman/list.php?error=' . urlencode($result['message']));
    }
    exit;
}

// Redirect default jika tidak ada aksi
header("Location: " . BASE_URL . "views/peminjaman/list.php");
exit;
?>