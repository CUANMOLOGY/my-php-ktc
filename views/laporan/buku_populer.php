<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../models/Buku.php';

$buku = new Buku($conn);
$bukuPopuler = $buku->getBukuPopuler();

// Data untuk JavaScript
$chartLabels = json_encode(array_column($bukuPopuler, 'judul'));
$chartData = json_encode(array_column($bukuPopuler, 'total_pinjam'));
?>

<!DOCTYPE html>
<html>
<head>
    <title>Laporan Buku Populer</title>
    <link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">
    <style>
        .chart-container {
            width: 80%;
            margin: 30px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="<?= url('assets/js/buku_populer.js') ?>"></script>
</head>
<body>
    <div class="container">
        <h1 class="page-title">Laporan Buku Paling Populer</h1>
        
        <div class="navigation-actions">
            <a href="<?= url('index.php') ?>" class="btn btn-secondary">Kembali ke Beranda</a>
            <a href="<?= url('views/laporan/cetak_buku_populer.php') ?>" class="btn btn-primary" target="_blank">Cetak Laporan</a>
        </div>

        <div class="chart-container">
            <canvas id="popularityChart"></canvas>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Peringkat</th>
                    <th>Judul Buku</th>
                    <th>Pengarang</th>
                    <th>Total Dipinjam</th>
                    <th>Persentase</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $total = array_sum(array_column($bukuPopuler, 'total_pinjam'));
                foreach ($bukuPopuler as $index => $buku): 
                    $persentase = ($buku['total_pinjam'] / $total) * 100;
                ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= htmlspecialchars($buku['judul']) ?></td>
                    <td><?= htmlspecialchars($buku['pengarang']) ?></td>
                    <td><?= $buku['total_pinjam'] ?></td>
                    <td>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?= $persentase ?>%"></div>
                            <span><?= round($persentase, 2) ?>%</span>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        // Data untuk chart
        const chartData = {
            labels: <?= $chartLabels ?>,
            data: <?= $chartData ?>
        };
    </script>
</body>
</html>