<?php
// Tentukan path file yang akan didownload
$filePath = __DIR__ . DIRECTORY_SEPARATOR . 'siswa.xlsx';

// Cek apakah file ada di folder
if (file_exists($filePath)) {
    // Set header untuk mengirimkan file sebagai download
    header('Content-Description: File Transfer');
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="siswa.xlsx"');
    header('Content-Length: ' . filesize($filePath));

    // Bersihkan output buffer (jika ada)
    ob_clean();
    flush();

    // Baca file dan kirim ke browser
    readfile($filePath);
    exit;
} else {
    // Jika file tidak ditemukan
    echo "File tidak ditemukan!";
}
?>
