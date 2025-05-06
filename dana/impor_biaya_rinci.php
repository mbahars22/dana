<?php
session_start();
set_time_limit(600); // Naikkan batas waktu eksekusi

include 'koneksi.php';
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['fileExcel']) && $_FILES['fileExcel']['error'] == 0) {
        $file = $_FILES['fileExcel']['tmp_name'];

        try {
            $spreadsheet = IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray();

            $firstRow = true;
            $stmt = $conn->prepare("REPLACE INTO biaya_siswa (nis, nama, kelas, kd_biaya, jumlah, thajaran) VALUES (?, ?, ?, ?, ?, ?)");
            $conn->begin_transaction();

            foreach ($data as $row) {
                if ($firstRow) {
                    $firstRow = false;
                    continue;
                }

                $nis       = isset($row[0]) ? trim($row[0]) : '';
                $nama      = isset($row[1]) ? trim($row[1]) : '';
                $kelas     = isset($row[2]) ? trim($row[2]) : '';
                $kd_biaya  = isset($row[3]) ? trim($row[3]) : '';
                $jumlahStr = isset($row[4]) ? trim($row[4]) : '';
                $thajaran  = isset($row[5]) ? trim($row[5]) : '';

                if ($nis === '' || $nama === '') {
                    continue;
                }

                $jumlah = floatval(preg_replace('/[^0-9]/', '', $jumlahStr));
                $stmt->bind_param("ssssds", $nis, $nama, $kelas, $kd_biaya, $jumlah, $thajaran);
                $stmt->execute();
            }

            $conn->commit();
            $stmt->close();
            $_SESSION['message'] = "✅ Impor data berhasil!";
        } catch (Exception $e) {
            $conn->rollback();
            $_SESSION['message'] = "❌ Gagal impor: " . $e->getMessage();
        }
    } else {
        $_SESSION['message'] = "❌ File tidak valid atau gagal upload.";
    }

    header("Location: tampil_biaya.php");
    exit();
}
?>
