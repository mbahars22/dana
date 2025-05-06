<?php
require 'functions.php';

if (!isset($_GET['nis']) || !isset($_GET['kd_transaksi'])) {
    die("Parameter tidak lengkap!");
}

$nis = $_GET['nis'];
$kd_transaksi = $_GET['kd_transaksi'];

// Ambil data pembayaran
$query = "SELECT * FROM pembayaran_siswa WHERE nis = ? AND kd_transaksi = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ss", $nis, $kd_transaksi);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$siswa = mysqli_fetch_assoc($result);

// Ambil data sekolah
$query_sekolah = "SELECT nama_sekolah, status, alamat FROM tb_profile LIMIT 1";
$result_sekolah = mysqli_query($conn, $query_sekolah);
$sekolah = mysqli_fetch_assoc($result_sekolah);

// QR Code
require 'vendor/phpqrcode/qrlib.php';
$qr_folder = 'qr/';
if (!file_exists($qr_folder)) {
    mkdir($qr_folder, 0777, true);
}

$qr_text = $kd_transaksi;
$qr_file = $qr_folder . 'qr_' . $kd_transaksi . '.png';
QRcode::png($qr_text, $qr_file, QR_ECLEVEL_L, 3);

// Fungsi terbilang
function terbilang($angka) {
    $angka = abs($angka);
    $bilangan = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];

    if ($angka < 12) return " " . $bilangan[$angka];
    elseif ($angka < 20) return terbilang($angka - 10) . " Belas";
    elseif ($angka < 100) return terbilang($angka / 10) . " Puluh" . terbilang($angka % 10);
    elseif ($angka < 200) return " Seratus" . terbilang($angka - 100);
    elseif ($angka < 1000) return terbilang($angka / 100) . " Ratus" . terbilang($angka % 100);
    elseif ($angka < 2000) return " Seribu" . terbilang($angka - 1000);
    elseif ($angka < 1000000) return terbilang($angka / 1000) . " Ribu" . terbilang($angka % 1000);
    elseif ($angka < 1000000000) return terbilang($angka / 1000000) . " Juta" . terbilang($angka % 1000000);
    elseif ($angka < 1000000000000) return terbilang($angka / 1000000000) . " Miliar" . terbilang($angka % 1000000000);
    else return "Angka terlalu besar";
}

$total_bayar = 0;
mysqli_data_seek($result, 0);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cetak Struk</title>
    <style>
        body { font-family: Courier, monospace; font-size: 11px; }
        .struk { width: 700px; margin: auto; }
        .head { text-align: left; }
        .judul { text-align: center; font-weight: bold; font-size: 14px; margin: 10px 0; }
        .info { margin-top: 5px; }
        /* table { width: 100%; border-collapse: collapse; margin-top: 10px; } */
        /* th { border: 1px solid #000; } */
        th, td { padding: 3px; text-align: center; } */
        .right { text-align: right; }
        .left { text-align: left; }
        .ttd { text-align: center; margin-top: 20px; }

        .no-border, .no-border * {
            border: none !important;
            outline: none !important;
            box-shadow: none !important;
            border-collapse: collapse;
        }

        .no-border td {
            padding: 2px 5px;
            font-size: 11px;
            vertical-align: top;
        }
        @media print {
    @page {
        size: 200mm 99mm; /* Lebar x Tinggi */
        margin: 5mm;
    }

    body {
        margin: 0;
    }

    .no-print { display: none; }
            .no-border, .no-border * {
                border: none !important;
                outline: none !important;
                box-shadow: none !important;
            }
            body, html {
    margin: 0;
    padding: 0;
  }
  .struk {
      margin-top: -10;
      padding-top: 0;
    }
    body, html {
      margin: 0;
      padding: 0;
    }
}
    
    </style>
</head>
<body onload="window.print()">
<div class="struk">
    <div class="head">
        <table class="no-border">
            <tr>
                <td class="left" width="30%"><b><?= strtoupper($sekolah['nama_sekolah']) ?></b><br>
                    <?= strtoupper($sekolah['status']) ?><br>
                    <?= ucwords(strtolower($sekolah['alamat'])) ?>
                </td>
                 <td width="30%" style="text-align: center; vertical-align: middle; font-weight: bold; font-size: 16px;">
                    KWITANSI <br>PEMBAYARAN
                </td>

                <td class="left"  width="10%">No Trans <br> NIS <br> Nama <br> Kelas</td>
                <td class="left">: <b><?= $siswa['kd_transaksi'] ?></b><br>: <b><?= $siswa['nis'] ?> </b> <br> : <?= $siswa['nama'] ?>
                <br>: <?= $siswa['kelas'] ?> </td>
                
            </tr>
        </table>
    </div>

    <!-- <div class="judul">KWITANSI PEMBAYARAN</div> -->

    <table style="border-collapse: collapse; width: 100%;">
    <tr>
        <th style="border-top: 1px solid #000; border-bottom: 1px solid #000; padding: 5px;">No</th>
        <th style="border-top: 1px solid #000; border-bottom: 1px solid #000; padding: 5px;">Kode Biaya</th>
        <th style="border-top: 1px solid #000; border-bottom: 1px solid #000; padding: 5px;">Th Ajaran</th>
        <th style="border-top: 1px solid #000; border-bottom: 1px solid #000; padding: 5px;">Jumlah</th>
        <th style="border-top: 1px solid #000; border-bottom: 1px solid #000; padding: 5px;">Bayar</th>
        <th style="border-top: 1px solid #000; border-bottom: 1px solid #000; padding: 5px;">Sisa</th>
        <th style="border-top: 1px solid #000; border-bottom: 1px solid #000; padding: 5px;">Method</th>
    </tr>
    <?php
    $no = 1;
    $row_count = 0;
    $total_bayar = 0;

    while ($row = mysqli_fetch_assoc($result)):
        $query_sisa = "SELECT jumlah FROM biaya_siswa WHERE nis = ? AND kd_biaya = ?";
        $stmt_sisa = mysqli_prepare($conn, $query_sisa);
        mysqli_stmt_bind_param($stmt_sisa, "ss", $nis, $row['kd_biaya']);
        mysqli_stmt_execute($stmt_sisa);
        $result_sisa = mysqli_stmt_get_result($stmt_sisa);
        $biaya = mysqli_fetch_assoc($result_sisa);
        $sisa = $biaya['jumlah'] ?? 0;

        $total_bayar += $row['bayar'];
        $row_count++;
    ?>
    <tr>
        <td style="padding: 5px;"><?= $no++ ?></td>
        <td style="padding: 5px; border-bottom: 0px;"><?= $row['kd_biaya'] ?></td>
        <td style="padding: 5px; border-bottom: 0px;"><?= $row['thajaran'] ?></td>
        <td style="padding: 5px; text-align: right;"><?= number_format($row['jumlah'], 0, ',', '.') ?></td>
        <td style="padding: 5px; text-align: right;"><?= number_format($row['bayar'], 0, ',', '.') ?></td>
        <td style="padding: 5px; text-align: right;"><?= number_format($sisa, 0, ',', '.') ?></td>
        <td style="padding: 5px;"><?= $row['method'] ?></td>
    </tr>
    <?php endwhile; ?>

    <?php for ($i = $row_count; $i < 5; $i++): ?>
    <tr>
        <td style="padding: 5px;">&nbsp;</td>
        <td style="padding: 5px;"></td>
        <td style="padding: 5px;"></td>
        <td style="padding: 5px;"></td>
        <td style="padding: 5px;"></td>
        <td style="padding: 5px;"></td>
        <td style="padding: 5px;"></td>
    </tr>
    <?php endfor; ?>

    <tr>
    
        <td colspan="2" style="padding: 5px; text-align: left; border-top: 1px solid #000; border-bottom: 1px solid #000;">
        <?php
        if (!empty($siswa['tgl_trans'])) {
            echo "Tanggal Transaksi: " . date('d-m-Y', strtotime($siswa['tgl_trans']));
        } else {
            echo "Tanggal transaksi tidak tersedia!";
        }
    ?>
    </td>
        <td colspan="2" style="padding: 5px; text-align: right; border-top: 1px solid #000; border-bottom: 1px solid #000;"><b>TOTAL</b></td>
        <td style="padding: 5px; text-align: right; border-top: 1px solid #000; border-bottom: 1px solid #000;"><b><?= number_format($total_bayar, 0, ',', '.') ?></b></td>
        <td colspan="2" style="border-top: 1px solid #000; border-bottom: 1px solid #000;"></td>
    </tr>
    <tr>
   
    <td colspan="7" style="height: 3px; border: none;">     
     </td>
</tr>
    <tr>
    <td colspan="3" style="padding: 2px; border: 1px solid #000; vertical-align: top; text-align: left;">
      <b>Terbilang: <?= trim(terbilang($total_bayar)) ?> Rupiah</b>
   </td>

        <td style="padding: 1px; border-top: 0px solid #000; border-bottom: 0px solid #000;">
        <img src="<?= $qr_file ?>" style="width: 50px; height: 50px;">

        </td>
        <td colspan="3" style="padding: 2px; border-top: 0px solid #000; border-bottom: 0px solid #000;">
            <b>Bendahara</b><br><br><br><?= $siswa['user'] ?>
        </td>
    </tr>
</table>


