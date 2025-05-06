<?php
include "koneksi.php";

$database = $db;
$backupFile = $database . "_" . date("Y-m-d_H-i-s") . ".sql";
$backupPath = __DIR__ . DIRECTORY_SEPARATOR . $backupFile;
$mysqldump = "C:\\xampp\\mysql\\bin\\mysqldump.exe";

// Pastikan hanya perintah dump masuk ke file
$command = "\"$mysqldump\" --user=$user --host=$host $database > \"$backupPath\"";

// Jalankan lewat shell_exec tanpa mencetak ke layar
shell_exec($command);

// Cek apakah berhasil dibuat dan berisi data
if (file_exists($backupPath) && filesize($backupPath) > 0) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/sql');
    header("Content-Disposition: attachment; filename=\"$backupFile\"");
    header('Content-Length: ' . filesize($backupPath));
    readfile($backupPath);
    unlink($backupPath);
    exit;
} else {
    echo "Gagal membuat backup database. File tidak terbentuk.";
}
?>
