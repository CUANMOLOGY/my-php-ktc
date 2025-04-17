<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Peminjaman.php';

$peminjaman = new Peminjaman($conn);
$id_peminjaman = $_GET['id'] ?? null;

if ($id_peminjaman) {
    $success = $peminjaman->kembalikan($id_peminjaman);
    header("Location: list.php?".($success ? "sukses=kembali" : "error=gagal_kembali"));
    exit;
}

header("Location: list.php");
?>