<?php
include '../koneksi.php'; // koneksi ke database
include 'opr_sidebar.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Transaksi</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container mt-3 ml-4">
        <div class="row">
            <!-- Kolom kiri kosong -->
            <div class="col-md-1"></div>
            <div class="col-11">
               <!-- Baris judul dan tombol tambah siswa -->
               <div class="row align-items-center mb-1">
                    <div class="col-md-6">
                        <h3 class="m-0">Data Master Jenis Biaya</h3>
                    </div>

    <!-- Kolom pencarian -->
        <div class="input-group mb-3" style="max-width: 400px;">
        <input type="text" id="search" class="form-control" placeholder="Cari berdasarkan Nama atau NIS...">
        <button class="btn btn-primary" type="button" id="btn-search">
            <i class="fas fa-search"></i>
        </button>
        </div>
    <!-- Tempat hasil pencarian ditampilkan -->
    <div id="hasil-pencarian">
        <!-- Isi tabel akan dimuat lewat AJAX -->
    </div>
</div>

<script>
$(document).ready(function() {
    // Fungsi untuk memuat data dari file pembayaran_data.php
    function loadData(keyword = '', page = 1) {
        $('#hasil-pencarian').html('<div class="text-center">Loading...</div>');
        $.get('opr_tampil_proses_bayar.php', { keyword: keyword, page: page }, function(data) {
            $('#hasil-pencarian').html(data);
        });
    }

    // Muat semua data saat halaman pertama kali dibuka
    loadData(); // Panggil loadData() tanpa keyword, jadi akan ambil semua data

    // Event: Ketik di kotak pencarian
    $('#search').on('keyup', function() {
        const keyword = $(this).val();
        loadData(keyword);
    });

    // Pencarian lewat tombol
    $('#btn-search').on('click', function() {
        const keyword = $('#search').val();
        loadData(keyword);
    });

    // Fungsi untuk ganti halaman pagination
    window.gantiHalaman = function(page) {
        const keyword = $('#search').val();
        loadData(keyword, page);
    };
});

window.hapusData = function(kd_transaksi, kd_biaya, nis, thajaran) {
    if (confirm("Yakin ingin menghapus data ini?")) {
        $.post("hapus_bayar.php", {
            kd_transaksi: kd_transaksi,
            kd_biaya: kd_biaya,
            nis: nis,
            thajaran: thajaran
        }, function(response) {
            alert(response);
            loadData($('#search').val()); // Refresh tampilan
        });
    }
};

// Fungsi untuk memuat ulang data setelah hapus atau pencarian
function loadData(keyword = '') {
    $('#hasil-pencarian').html('<div class="text-center">Loading...</div>');
    $.get('opr_tampil_proses_bayar.php', { keyword: keyword }, function(data) {
        $('#hasil-pencarian').html(data);
    });
}
</script>
</body>
</html>
