<?php
session_start();
include 'koneksi.php';

echo '
<html>
<head>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
';

if (isset($_FILES['sql_file']) && $_FILES['sql_file']['error'] == 0) {
    $tmpFilePath = $_FILES['sql_file']['tmp_name'];
    $fileRestore = 'restore_temp.sql';

    if (move_uploaded_file($tmpFilePath, $fileRestore)) {
        if ($conn->connect_error) {
            echo "<script>
                Swal.fire({ icon: 'error', title: 'Koneksi Gagal', text: '" . addslashes($conn->connect_error) . "' });
            </script>";
            exit;
        }

        // Baca file .sql dan jalankan query satu per satu
        $templine = '';
        $lines = file($fileRestore);
        foreach ($lines as $line) {
            $trimmedLine = trim($line);

            // Lewati baris komentar dan baris error dari backup
            if ($trimmedLine == '' || str_starts_with($trimmedLine, '--') || str_starts_with($trimmedLine, 'Command') || str_starts_with($trimmedLine, 'Array')) {
                continue;
            }

            $templine .= $line;
            if (substr(trim($line), -1) == ';') {
                if (!$conn->query($templine)) {
                    echo "<script>
                        Swal.fire({
                            icon: 'error',
                            title: 'Restore Gagal',
                            html: 'Terjadi kesalahan saat eksekusi query: <br><pre>" . addslashes($conn->error) . "</pre>'
                        });
                    </script>";
                    unlink($fileRestore);
                    exit;
                }
                $templine = '';
            }
        }

        $conn->close();
        unlink($fileRestore);

        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Restore Berhasil!',
                text: 'Database berhasil di-restore.',
                confirmButtonText: 'OK'
            }).then(() => { window.location.href = 'admin_dashboard2.php'; });
        </script>";
    } else {
        echo "<script>
            Swal.fire({ icon: 'error', title: 'Upload Gagal', text: 'File gagal dipindahkan ke server.' });
        </script>";
    }
} else {
    echo "<script>
        Swal.fire({ icon: 'error', title: 'Upload Error', text: 'File tidak valid atau gagal upload.' });
    </script>";
}
echo '</body></html>';
?>
