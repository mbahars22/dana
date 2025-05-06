<?php
session_start();
include 'koneksi.php';
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['fileExcel']) && $_FILES['fileExcel']['error'] == 0) {
        $file = $_FILES['fileExcel']['tmp_name'];
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray();

        $firstRow = true; // Untuk melewati header
        $stmt = $conn->prepare("INSERT INTO jenis_transaksi (kd_biaya, volume, kelas, nama_biaya, th_ajaran, jumlah) VALUES (?, ?, ?, ?, ?, ?)");

        foreach ($data as $row) {
            if ($firstRow) {
                $firstRow = false;
                continue;
            }

            // Trim semua kolom terlebih dahulu
            $kd_biaya = trim($row[0]);
            $volume = trim($row[1]);
            $kelas = trim($row[2]);
            $nama_biaya = trim($row[3]);
            $th_ajaran = trim($row[4]);
            $jumlah_raw = isset($row[5]) ? $row[5] : '';

            // Skip jika semua kolom utama kosong (menghindari baris kosong)
            if (empty($kd_biaya) && empty($volume) && empty($kelas) && empty($nama_biaya) && empty($th_ajaran) && empty($jumlah_raw)) {
                continue;
            }

            // Bersihkan format jumlah
            $jumlah = preg_replace('/[^0-9]/', '', $jumlah_raw);
            $jumlah = ($jumlah === "") ? 0 : floatval($jumlah);

            // Bind dan eksekusi
            $stmt->bind_param("sssssd", $kd_biaya, $volume, $kelas, $nama_biaya, $th_ajaran, $jumlah);
            if (!$stmt->execute()) {
                $_SESSION['message'] = "Gagal menyimpan data: " . $stmt->error;
                break;
            }
        }

        $stmt->close();
        if (!isset($_SESSION['message'])) {
            $_SESSION['message'] = "Impor data berhasil!";
        }
    } else {
        $_SESSION['message'] = "Gagal mengunggah file.";
    }
}

header("Location: tampil_mst_biaya.php");
exit();
?>
