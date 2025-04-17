<?php
require_once '../models/Buku.php';
require_once '../config/database.php';

$buku = new Buku($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['tambah'])) {
        $buku->tambah($_POST['judul'], $_POST['pengarang'], $_POST['tahun_terbit'], $_POST['stok']);
        header("Location: ../views/buku/list.php?sukses=tambah");
    } elseif (isset($_POST['update'])) {
        $buku->update($_POST['id'], $_POST['judul'], $_POST['pengarang'], $_POST['tahun_terbit'], $_POST['stok']);
        header("Location: ../views/buku/list.php?sukses=update");
    }
}

if (isset($_GET['hapus'])) {
    $success = $buku->hapus($_GET['hapus']);
    
    if ($success) {
        header("Location: ../views/buku/list.php?sukses=hapus");
    } else {
        header("Location: ../views/buku/list.php?error=buku_sedang_dipinjam");
    }
    exit;
}