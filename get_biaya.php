<?php
include 'koneksi.php';

$kelas = $_GET['kelas'] ?? '';
if (!$kelas) {
    die("Kelas tidak ditemukan.");
}

// Ambil data biaya berdasarkan kelas dari tabel jenis_transaksi
$query = mysqli_query($conn, "SELECT * FROM jenis_transaksi WHERE kelas = '$kelas'");

if (mysqli_num_rows($query) == 0) {
    die("Data biaya tidak ditemukan untuk kelas: " . $kelas);
}

$biayaVolume = "";   // Untuk biaya yang punya volume > 1 (misalnya cicilan per bulan)
$biayaSingle = "";   // Untuk biaya yang hanya sekali bayar (volume = 1)

$nama_bulan = ["01" => "JAN", "02" => "FEB", "03" => "MAR", "04" => "APR", "05" => "MEI", "06" => "JUN", "07" => "JUL", "08" => "AGU", "09" => "SEP", "10" => "OKT", "11" => "NOV", "12" => "DES"];

while ($row = mysqli_fetch_assoc($query)) {
    $kd_biaya    = $row['kd_biaya'];
    $nama_biaya  = $row['nama_biaya'];
    $jumlah      = $row['jumlah'];
    $th_ajaran   = $row['th_ajaran'];
    $volume      = is_numeric($row['volume']) ? (int)$row['volume'] : 1;

    $th_awal  = substr($th_ajaran, 0, 2); // 2 digit pertama
    $th_akhir = substr($th_ajaran, -2);   // 2 digit terakhir

    if ($volume > 1) {
        // Biaya cicilan (misalnya SPP)
        $biayaVolume .= "<div class='card mb-2'>
            <div class='card-header bg-primary text-white p-2'>
                <small><strong>{$nama_biaya} ({$volume}x Cicilan)</strong></small>
            </div>
            <div class='card-body p-2'>
                <table class='table table-sm table-bordered'>
                    <thead class='table-light'>
                        <tr>
                            <th class='w-50'>Kode</th>
                            <th class='w-50'>Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>";

        for ($i = 1; $i <= $volume; $i++) {
            $bulan = str_pad($i, 2, '0', STR_PAD_LEFT);
            $nama_bln = $nama_bulan[$bulan] ?? "Bulan-$i";

            // Tentukan tahun tambahan berdasarkan bulan (Juli ke Desember = tahun awal)
            $tahun_append = ($i >= 7) ? $th_awal : $th_akhir;

            $kd_biaya_bulan = $kd_biaya . "-" . $nama_bln . "-" . $tahun_append;

            $biayaVolume .= "
                <tr>
                    <td>
                        <input type='text' class='form-control form-control-sm w-100' name='kd_biaya[]' value='$kd_biaya_bulan' readonly>
                    </td>
                    <td>
                        <input type='number' class='form-control form-control-sm text-end' name='jumlah[]' value='$jumlah'>
                    </td>
                </tr>";
        }

        $biayaVolume .= "</tbody></table></div></div>";

    } else {
        // Biaya sekali bayar
        $biayaSingle .= "
            <div class='card mb-2'>
                <div class='card-header bg-success text-white p-2'>
                    <small><strong>{$nama_biaya}</strong></small>
                </div>
                <div class='card-body p-2'>
                    <div class='d-flex justify-content-between gap-2'>
                        <div class='w-60'>
                            <label class='form-label'><small>Kode:</small></label>
                            <input type='text' class='form-control form-control-sm' name='kd_biaya[]' value='$kd_biaya' readonly>
                        </div>
                        <div class='w-40'>
                            <label class='form-label'><small>Jumlah:</small></label>
                            <input type='number' class='form-control form-control-sm text-end' name='jumlah[]' value='$jumlah' readonly>
                        </div>
                    </div>
                </div>
            </div>";
    }
}

// Gabungkan tampilan jadi 2 kolom
$biayaHTML = "<div class='container'>
    <div class='row'>
        <div class='col-md-8'>$biayaVolume</div>   <!-- Biaya cicilan -->
        <div class='col-md-4'>$biayaSingle</div>   <!-- Biaya sekali bayar -->
    </div>
</div>";

echo $biayaHTML;
?>
