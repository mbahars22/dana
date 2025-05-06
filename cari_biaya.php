<?php
include 'koneksi.php'; // Pastikan koneksi database benar

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nis = isset($_POST['nis']) ? trim($_POST['nis']) : '';

    if (empty($nis)) {
        echo json_encode(['success' => false, 'message' => 'NIS tidak boleh kosong']);
        exit;
    }

    // Query hanya mengambil kd_biaya dan jumlah
    $query = "SELECT * FROM biaya_siswa WHERE nis = ?";
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Query error: ' . $conn->error]);
        exit;
    }

    $stmt->bind_param("s", $nis);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $biayaList = [];

        while ($row = $result->fetch_assoc()) {
            $biayaList[] = [
                'kd_biaya' => $row['kd_biaya'],
                'jumlah' => $row['jumlah']
            ];
        }

        echo json_encode(['success' => true, 'data' => $biayaList]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Data biaya tidak ditemukan untuk NIS ini']);
    }
}


?>
