public function bukuPopuler() {
    $sql = "SELECT b.judul, COUNT(*) as total_pinjam 
            FROM detail_peminjaman dp
            JOIN buku b ON dp.id_buku = b.id_buku
            GROUP BY dp.id_buku
            ORDER BY total_pinjam DESC
            LIMIT 5";
    $result = $this->conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}