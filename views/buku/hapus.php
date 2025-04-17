<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Buku.php';

$buku = new Buku($conn);
$id = $_GET['id'] ?? null;

if ($id) {
    $success = $buku->hapus($id);
    
    if ($success) {
        header("Location: list.php?sukses=hapus");
    } else {
        header("Location: list.php?error=buku_sedang_dipinjam");
    }
    exit;
}

header("Location: list.php");
?>