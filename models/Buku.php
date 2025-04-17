<?php
class Buku {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function tambah($judul, $pengarang, $penerbit, $tahun_terbit, $isbn, $kategori, $stok) {
        $sql = "INSERT INTO buku (judul, pengarang, penerbit, tahun_terbit, isbn, kategori, stok) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssisssi", $judul, $pengarang, $penerbit, $tahun_terbit, $isbn, $kategori, $stok);
        return $stmt->execute();
    }

    public function getAll($filter = []) {
        $sql = "SELECT * FROM buku WHERE 1=1";
        $params = [];
        $types = "";
        
        if (!empty($filter['kategori'])) {
            $sql .= " AND kategori = ?";
            $params[] = $filter['kategori'];
            $types .= "s";
        }
        
        if (!empty($filter['search'])) {
            $sql .= " AND (judul LIKE ? OR pengarang LIKE ? OR penerbit LIKE ?)";
            $searchTerm = "%{$filter['search']}%";
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm]);
            $types .= "sss";
        }
        
        if (isset($filter['stok_min'])) {
            $sql .= " AND stok >= ?";
            $params[] = $filter['stok_min'];
            $types .= "i";
        }
        
        $sql .= " ORDER BY judul ASC";
        
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getAvailable() {
        return $this->getAll(['stok_min' => 1]);
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM buku WHERE id_buku = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function update($id, $judul, $pengarang, $penerbit, $tahun_terbit, $isbn, $kategori, $stok) {
        $sql = "UPDATE buku SET judul=?, pengarang=?, penerbit=?, tahun_terbit=?, isbn=?, kategori=?, stok=? 
                WHERE id_buku=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssssssi", $judul, $pengarang, $penerbit, $tahun_terbit, $isbn, $kategori, $stok, $id);
        return $stmt->execute();
    }

    public function hapus($id) {
        // Cek apakah buku sedang dipinjam
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) as total 
            FROM detail_peminjaman dp
            JOIN peminjaman p ON dp.id_peminjaman = p.id_peminjaman
            WHERE dp.id_buku = ? AND p.status = 'dipinjam'
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        if ($result['total'] > 0) {
            return false;
        }
        
        // Hapus buku
        $stmt = $this->conn->prepare("DELETE FROM buku WHERE id_buku = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function getBukuPopuler($limit = 5, $tahun = null) {
        $sql = "SELECT b.id_buku, b.judul, b.pengarang, COUNT(dp.id_buku) as total_pinjam
                FROM detail_peminjaman dp
                JOIN buku b ON dp.id_buku = b.id_buku
                JOIN peminjaman p ON dp.id_peminjaman = p.id_peminjaman";
        
        if ($tahun) {
            $sql .= " WHERE YEAR(p.tanggal_pinjam) = ?";
        }
        
        $sql .= " GROUP BY dp.id_buku
                ORDER BY total_pinjam DESC
                LIMIT ?";
                
        $stmt = $this->conn->prepare($sql);
        
        if ($tahun) {
            $stmt->bind_param("ii", $tahun, $limit);
        } else {
            $stmt->bind_param("i", $limit);
        }
        
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getKategori() {
        $result = $this->conn->query("SELECT DISTINCT kategori FROM buku WHERE kategori IS NOT NULL");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function updateStok($id_buku, $perubahan) {
        $this->conn->begin_transaction();
        try {
            $stmt = $this->conn->prepare("UPDATE buku SET stok = stok + ? WHERE id_buku = ?");
            $stmt->bind_param("ii", $perubahan, $id_buku);
            $stmt->execute();
            
            // Cek stok tidak negatif
            $stmt = $this->conn->prepare("SELECT stok FROM buku WHERE id_buku = ?");
            $stmt->bind_param("i", $id_buku);
            $stmt->execute();
            $stok = $stmt->get_result()->fetch_assoc()['stok'];
            
            if ($stok < 0) {
                throw new Exception("Stok tidak boleh negatif");
            }
            
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }
}
?>