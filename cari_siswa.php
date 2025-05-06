<?php
include 'koneksi.php'; // Pastikan file koneksi ke database ada

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nis = $_POST['nis'];

    // Query untuk mencari data siswa berdasarkan NIS
    $query = "SELECT nama, kelas FROM siswa WHERE nis = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $nis);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode(['success' => true, 'nama' => $row['nama'], 'kelas' => $row['kelas']]);
    } else {
        echo json_encode(['success' => false]);
    }
}
?>
