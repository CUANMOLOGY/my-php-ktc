<?php
require_once '../models/Anggota.php';
$anggota = new Anggota();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $anggota->tambah($nama, $alamat);
    header("Location: ../views/anggota/list.php");
    exit();
}
?>