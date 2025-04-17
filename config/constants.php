<?php
// Base URL configuration
define('BASE_URL', '/perpus/');

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'perpus');

// Error Codes
define('ERROR_STOK_TIDAK_CUKUP', 6);
define('ERROR_ANGGOTA_BLOCKED', 7);
define('ERROR_BUKU_TIDAK_ADA', 8);
define('ERROR_ANGGOTA_TIDAK_ADA', 9);
define('DENDA_PER_HARI', 5000); // Rp 5000 per hari
define('ERROR_ANGGOTA_TIDAK_VALID', 1001);
define('ERROR_UPDATE_STOK', 1005);
define('ERROR_PINJAMAN_TIDAK_ADA', 1006);
define('ERROR_SUDAH_DIKEMBALIKAN', 1007);

// Helper function for generating URLs
function url($path) {
    return BASE_URL . ltrim($path, '/');
}
?>