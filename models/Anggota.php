<?php
require_once __DIR__ . '/../config/database.php';

class Anggota {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getAllActive() {
        try {
            // Cek apakah kolom status ada
            $checkColumn = $this->conn->query("SHOW COLUMNS FROM anggota LIKE 'status'");
            
            if ($checkColumn->num_rows > 0) {
                $sql = "SELECT * FROM anggota WHERE status = 'aktif' ORDER BY nama";
            } else {
                $sql = "SELECT * FROM anggota ORDER BY nama";
            }
            
            $result = $this->conn->query($sql);
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            error_log("Error in getAllActive: " . $e->getMessage());
            return [];
        }
    }

    public function cekStatusAnggota($id_anggota) {
        try {
            $checkColumn = $this->conn->query("SHOW COLUMNS FROM anggota LIKE 'status'");
            $hasStatusColumn = ($checkColumn->num_rows > 0);
            
            $sql = "SELECT 
                    " . ($hasStatusColumn ? "a.status" : "'aktif' as status") . ",
                    (SELECT COUNT(*) FROM peminjaman 
                     WHERE id_anggota = ? AND status = 'dipinjam' 
                     AND tanggal_kembali < CURDATE()) as total_keterlambatan
                FROM anggota a
                WHERE a.id_anggota = ?";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ii", $id_anggota, $id_anggota);
            $stmt->execute();
            return $stmt->get_result()->fetch_assoc();
        } catch (Exception $e) {
            error_log("Error in cekStatusAnggota: " . $e->getMessage());
            return [
                'status' => 'aktif',
                'total_keterlambatan' => 0
            ];
        }
    }

    public function getAll() {
        $sql = "SELECT * FROM anggota";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM anggota WHERE id_anggota = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function tambah($nama, $alamat, $telepon, $email) {
        // Validasi input
        if (empty($nama) || empty($alamat)) {
            return false;
        }

        // Validasi format email jika diisi
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        // Cek apakah kolom status ada
        $checkColumn = $this->conn->query("SHOW COLUMNS FROM anggota LIKE 'status'");
        $hasStatusColumn = ($checkColumn->num_rows > 0);
        
        try {
            if ($hasStatusColumn) {
                $sql = "INSERT INTO anggota (nama, alamat, telepon, email, status) 
                        VALUES (?, ?, ?, ?, 'aktif')";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("ssss", $nama, $alamat, $telepon, $email);
            } else {
                $sql = "INSERT INTO anggota (nama, alamat, telepon, email) 
                        VALUES (?, ?, ?, ?)";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("ssss", $nama, $alamat, $telepon, $email);
            }
            
            $result = $stmt->execute();
            
            // Mengembalikan ID anggota yang baru ditambahkan jika berhasil
            return $result ? $this->conn->insert_id : false;
            
        } catch (Exception $e) {
            error_log("Error in tambah: " . $e->getMessage());
            return false;
        }
    }

    public function update($id_anggota, $nama, $alamat, $telepon, $email) {
        // Validasi input
        if (empty($nama) || empty($alamat)) {
            return false;
        }

        // Validasi format email jika diisi
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        try {
            $sql = "UPDATE anggota SET 
                    nama = ?, 
                    alamat = ?, 
                    telepon = ?, 
                    email = ? 
                    WHERE id_anggota = ?";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ssssi", $nama, $alamat, $telepon, $email, $id_anggota);
            
            return $stmt->execute();
            
        } catch (Exception $e) {
            error_log("Error in update: " . $e->getMessage());
            return false;
        }
    }

    public function hapus($id_anggota) {
        try {
            // Cek apakah ada peminjaman yang aktif
            $sqlCek = "SELECT COUNT(*) as total FROM peminjaman 
                       WHERE id_anggota = ? AND status = 'dipinjam'";
            
            $stmtCek = $this->conn->prepare($sqlCek);
            $stmtCek->bind_param("i", $id_anggota);
            $stmtCek->execute();
            $result = $stmtCek->get_result()->fetch_assoc();
            
            if ($result['total'] > 0) {
                return false; // Tidak bisa hapus jika ada peminjaman aktif
            }
            
            // Cek apakah menggunakan soft delete (kolom status)
            $checkColumn = $this->conn->query("SHOW COLUMNS FROM anggota LIKE 'status'");
            $hasStatusColumn = ($checkColumn->num_rows > 0);
            
            if ($hasStatusColumn) {
                $sql = "UPDATE anggota SET status = 'nonaktif' WHERE id_anggota = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("i", $id_anggota);
            } else {
                $sql = "DELETE FROM anggota WHERE id_anggota = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("i", $id_anggota);
            }
            
            return $stmt->execute();
            
        } catch (Exception $e) {
            error_log("Error in hapus: " . $e->getMessage());
            return false;
        }
    }
}