<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Anggota.php';

$anggota = new Anggota($conn);

$id = $_GET['id'] ?? 0;

if (!is_numeric($id) || $id <= 0) {
    header("Location: list.php?error=ID tidak valid");
    exit;
}

if ($anggota->hapus($id)) {
    header("Location: list.php?success=Anggota berhasil dihapus");
} else {
    header("Location: list.php?error=Gagal menghapus anggota");
}
exit;