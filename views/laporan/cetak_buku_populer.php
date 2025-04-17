<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Buku.php';

$buku = new Buku($conn);
$bukuPopuler = $buku->getBukuPopuler(10); // Ambil 10 buku teratas
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cetak Laporan Buku Populer</title>
    <style>
        body { font-family: Arial; font-size: 12pt; }
        h1 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .footer { margin-top: 50px; text-align: right; }
    </style>
</head>
<body>
    <h1>Laporan Buku Paling Populer</h1>
    <p>Periode: <?= date('d F Y') ?></p>
    
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Judul Buku</th>
                <th>Pengarang</th>
                <th>Total Dipinjam</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bukuPopuler as $index => $buku): ?>
            <tr>
                <td><?= $index + 1 ?></td>
                <td><?= htmlspecialchars($buku['judul']) ?></td>
                <td><?= htmlspecialchars($buku['pengarang']) ?></td>
                <td><?= $buku['total_pinjam'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <div class="footer">
        <p>Mengetahui,</p>
        <br><br><br>
        <p>_________________________</p>
        <p>Petugas Perpustakaan</p>
    </div>

    <script>
        window.print();
    </script>
</body>
</html>