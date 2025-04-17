<?php
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/Anggota.php';
require_once __DIR__ . '/Buku.php';

class Peminjaman {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Meminjam buku
     * @param int $id_anggota ID anggota yang meminjam
     * @param int $id_buku ID buku yang dipinjam
     * @param int $jumlah Jumlah buku yang dipinjam
     * @param string $tanggal_kembali Format Y-m-d
     * @return array|int ID peminjaman jika sukses, array error jika gagal
     */
    public function pinjam($id_anggota, $id_buku, $jumlah, $tanggal_kembali) {
        $this->conn->begin_transaction();
        try {
            // Validasi data
            $this->validasiDataPeminjaman($id_anggota, $id_buku, $jumlah);
            
            // Catat peminjaman
            $id_peminjaman = $this->catatPeminjaman($id_anggota, $tanggal_kembali);
            
            // Catat detail peminjaman
            $this->catatDetailPeminjaman($id_peminjaman, $id_buku, $jumlah);
            
            // Kurangi stok buku
            $this->kurangiStokBuku($id_buku, $jumlah);
            
            $this->conn->commit();
            return $id_peminjaman;
        } catch (Exception $e) {
            $this->conn->rollback();
            return [
                'error' => true,
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Mengembalikan buku yang dipinjam
     * @param int $id_peminjaman ID peminjaman
     * @return array Hasil proses pengembalian
     */
    public function kembalikan($id_peminjaman) {
        $this->conn->begin_transaction();
        try {
            $peminjaman = $this->getDataPeminjaman($id_peminjaman);
            $this->validasiPengembalian($peminjaman);
            
            $denda = $this->hitungDenda($peminjaman['tanggal_kembali']);
            $detail = $this->getDetailPeminjaman($id_peminjaman);
            
            $this->kembalikanStokBuku($detail);
            $this->updateStatusPengembalian($id_peminjaman, $denda);
            
            $this->conn->commit();
            return [
                'success' => true,
                'denda' => $denda,
                'peminjaman' => $peminjaman,
                'detail' => $detail
            ];
        } catch (Exception $e) {
            $this->conn->rollback();
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ];
        }
    }

    /**
     * Mendapatkan detail peminjaman
     * @param int $id_peminjaman ID peminjaman
     * @return array Detail peminjaman
     */
    public function getDetailPeminjaman($id_peminjaman) {
        $stmt = $this->conn->prepare("
            SELECT dp.*, b.judul, b.pengarang, b.kategori 
            FROM detail_peminjaman dp 
            JOIN buku b ON dp.id_buku = b.id_buku 
            WHERE dp.id_peminjaman = ?
        ");
        $stmt->bind_param("i", $id_peminjaman);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Mendapatkan statistik peminjaman
     * @param int|null $tahun Tahun yang ingin dilihat statistiknya
     * @return array Statistik peminjaman
     */
    public function getStatistikPeminjaman($tahun = null) {
        $sql = "SELECT 
                    MONTH(tanggal_pinjam) as bulan,
                    COUNT(id_peminjaman) as total_peminjaman,
                    SUM(
                        CASE 
                            WHEN status = 'dikembalikan' AND tanggal_kembali < tanggal_dikembalikan THEN 1
                            ELSE 0
                        END
                    ) as total_terlambat,
                    SUM(denda) as total_denda
                FROM peminjaman";
        
        if ($tahun) {
            $sql .= " WHERE YEAR(tanggal_pinjam) = ?";
        }
        
        $sql .= " GROUP BY MONTH(tanggal_pinjam)
                ORDER BY bulan";
        
        $stmt = $this->conn->prepare($sql);
        
        if ($tahun) {
            $stmt->bind_param("i", $tahun);
        }
        
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Mendapatkan daftar peminjaman aktif
     * @param int|null $id_anggota Filter by anggota
     * @return array Daftar peminjaman aktif
     */
    public function getPeminjamanAktif($id_anggota = null) {
        $sql = "SELECT p.*, a.nama as nama_anggota, 
                GROUP_CONCAT(b.judul SEPARATOR ', ') as buku,
                SUM(dp.jumlah) as total_buku
                FROM peminjaman p
                JOIN anggota a ON p.id_anggota = a.id_anggota
                JOIN detail_peminjaman dp ON p.id_peminjaman = dp.id_peminjaman
                JOIN buku b ON dp.id_buku = b.id_buku
                WHERE p.status = 'dipinjam'";
        
        if ($id_anggota) {
            $sql .= " AND p.id_anggota = ?";
        }
        
        $sql .= " GROUP BY p.id_peminjaman
                ORDER BY p.tanggal_kembali ASC";
        
        $stmt = $this->conn->prepare($sql);
        
        if ($id_anggota) {
            $stmt->bind_param("i", $id_anggota);
        }
        
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Mendapatkan riwayat peminjaman dengan filter
     * @param array $filter Filter untuk pencarian
     * @return array Riwayat peminjaman
     */
    public function getRiwayatPeminjaman($filter = []) {
        $sql = "SELECT p.*, a.nama as nama_anggota, 
                GROUP_CONCAT(b.judul SEPARATOR ', ') as buku,
                SUM(dp.jumlah) as total_buku,
                p.denda,
                CASE 
                    WHEN p.status = 'dikembalikan' THEN 'Sudah Kembali'
                    WHEN p.tanggal_kembali < CURDATE() THEN 'Terlambat'
                    ELSE 'Dipinjam'
                END as status_pinjam
                FROM peminjaman p
                JOIN anggota a ON p.id_anggota = a.id_anggota
                JOIN detail_peminjaman dp ON p.id_peminjaman = dp.id_peminjaman
                JOIN buku b ON dp.id_buku = b.id_buku
                WHERE 1=1";
        
        $params = [];
        $types = "";
        
        if (!empty($filter['id_anggota'])) {
            $sql .= " AND p.id_anggota = ?";
            $params[] = $filter['id_anggota'];
            $types .= "i";
        }
        
        if (!empty($filter['status'])) {
            if ($filter['status'] === 'aktif') {
                $sql .= " AND p.status = 'dipinjam'";
            } else {
                $sql .= " AND p.status = 'dikembalikan'";
            }
        }
        
        if (!empty($filter['tahun'])) {
            $sql .= " AND YEAR(p.tanggal_pinjam) = ?";
            $params[] = $filter['tahun'];
            $types .= "i";
        }
        
        if (!empty($filter['search'])) {
            $sql .= " AND (a.nama LIKE ? OR b.judul LIKE ?)";
            $searchTerm = "%{$filter['search']}%";
            $params = array_merge($params, [$searchTerm, $searchTerm]);
            $types .= "ss";
        }
        
        $sql .= " GROUP BY p.id_peminjaman
                ORDER BY p.tanggal_pinjam DESC";
        
        $stmt = $this->conn->prepare($sql);
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /* ========== METHOD PRIVATE ========== */
    
    /**
     * Validasi data sebelum peminjaman
     */
    private function validasiDataPeminjaman($id_anggota, $id_buku, $jumlah) {
        $anggota = new Anggota($this->conn);
        $statusAnggota = $anggota->cekStatusAnggota($id_anggota);
        
        if (!$statusAnggota || $statusAnggota['status'] !== 'aktif') {
            throw new Exception("Anggota tidak aktif atau tidak ditemukan", ERROR_ANGGOTA_TIDAK_VALID);
        }
        
        if ($statusAnggota['total_keterlambatan'] > 0) {
            throw new Exception("Anggota memiliki peminjaman yang terlambat", ERROR_ANGGOTA_BLOCKED);
        }
        
        $buku = new Buku($this->conn);
        $bukuData = $buku->getById($id_buku);
        
        if (!$bukuData) {
            throw new Exception("Buku tidak ditemukan", ERROR_BUKU_TIDAK_ADA);
        }
        
        if ($bukuData['stok'] < $jumlah) {
            throw new Exception("Stok buku tidak mencukupi", ERROR_STOK_TIDAK_CUKUP);
        }
    }
    
    /**
     * Mencatat data peminjaman ke database
     */
    private function catatPeminjaman($id_anggota, $tanggal_kembali) {
        $stmt = $this->conn->prepare("
            INSERT INTO peminjaman 
            (id_anggota, tanggal_pinjam, tanggal_kembali, status) 
            VALUES (?, CURDATE(), ?, 'dipinjam')
        ");
        $stmt->bind_param("is", $id_anggota, $tanggal_kembali);
        $stmt->execute();
        return $this->conn->insert_id;
    }
    
    /**
     * Mencatat detail buku yang dipinjam
     */
    private function catatDetailPeminjaman($id_peminjaman, $id_buku, $jumlah) {
        $stmt = $this->conn->prepare("
            INSERT INTO detail_peminjaman 
            (id_peminjaman, id_buku, jumlah) 
            VALUES (?, ?, ?)
        ");
        $stmt->bind_param("iii", $id_peminjaman, $id_buku, $jumlah);
        $stmt->execute();
    }
    
    /**
     * Mengurangi stok buku
     */
    private function kurangiStokBuku($id_buku, $jumlah) {
        $buku = new Buku($this->conn);
        if (!$buku->updateStok($id_buku, -$jumlah)) {
            throw new Exception("Gagal mengurangi stok buku", ERROR_UPDATE_STOK);
        }
    }
    
    /**
     * Mengambil data peminjaman
     */
    private function getDataPeminjaman($id_peminjaman) {
        $stmt = $this->conn->prepare("
            SELECT p.*, a.nama 
            FROM peminjaman p
            JOIN anggota a ON p.id_anggota = a.id_anggota
            WHERE p.id_peminjaman = ? 
            FOR UPDATE
        ");
        $stmt->bind_param("i", $id_peminjaman);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        if (!$result) {
            throw new Exception("Peminjaman tidak ditemukan", ERROR_PINJAMAN_TIDAK_ADA);
        }
        
        return $result;
    }
    
    /**
     * Validasi sebelum pengembalian
     */
    private function validasiPengembalian($peminjaman) {
        if ($peminjaman['status'] === 'dikembalikan') {
            throw new Exception("Buku sudah dikembalikan sebelumnya", ERROR_SUDAH_DIKEMBALIKAN);
        }
    }
    
    /**
     * Menghitung denda keterlambatan
     */
    private function hitungDenda($tanggal_kembali) {
        $denda = 0;
        $tglKembali = new DateTime($tanggal_kembali);
        $today = new DateTime();
        
        if ($today > $tglKembali) {
            $selisih = $today->diff($tglKembali);
            $denda = $selisih->days * DENDA_PER_HARI;
        }
        
        return $denda;
    }
    
    /**
     * Mengembalikan stok buku
     */
    private function kembalikanStokBuku($detail) {
        $buku = new Buku($this->conn);
        foreach ($detail as $item) {
            if (!$buku->updateStok($item['id_buku'], $item['jumlah'])) {
                throw new Exception("Gagal mengembalikan stok buku", ERROR_UPDATE_STOK);
            }
        }
    }
    
    /**
     * Update status pengembalian
     */
    private function updateStatusPengembalian($id_peminjaman, $denda) {
        $stmt = $this->conn->prepare("
            UPDATE peminjaman 
            SET status = 'dikembalikan', 
                tanggal_dikembalikan = NOW(), 
                denda = ? 
            WHERE id_peminjaman = ?
        ");
        $stmt->bind_param("di", $denda, $id_peminjaman);
        $stmt->execute();
    }
}