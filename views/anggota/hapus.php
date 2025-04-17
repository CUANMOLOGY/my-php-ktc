<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Anggota.php';

$anggota = new Anggota($conn);

$id = $_GET['id'] ?? 0;

if ($anggota->hapus($id)) {
    header("Location: list.php?sukses=hapus");
} else {
    header("Location: list.php?error=hapus");
}
exit;