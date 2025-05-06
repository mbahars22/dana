<!DOCTYPE html>
<html lang="id">
<?php
// session_start();
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'koneksi.php';
include 'sidebar.php';


// Pastikan hanya admin yang bisa mengakses halaman ini
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}
    $total_harian = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(jumlah) AS total FROM pembayaran_siswa WHERE DATE(tgl_trans) = CURDATE()"))['total'] ?? 0;
    $query = mysqli_query($conn, "SELECT SUM(jumlah) AS total FROM pembayaran_siswa WHERE MONTH(tgl_trans) = MONTH(CURDATE()) AND YEAR(tgl_trans) = YEAR(CURDATE())");
    if (!$query) {
        die("Query Error: " . mysqli_error($conn)); // Cek error MySQL
}

$data = mysqli_fetch_assoc($query);
$total_bulanan = $data['total'] ?? 0;

echo "Total Bulanan: Rp " . number_format($total_bulanan, 0, ',', '.');
    $total_tahunan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(jumlah) AS total FROM pembayaran_siswa WHERE YEAR(tgl_trans) = YEAR(CURDATE())"))['total'] ?? 0;

    // Ambil data per kd_biaya
    $biaya_query = mysqli_query($conn, "SELECT kd_biaya, SUM(jumlah) AS total FROM pembayaran_siswa GROUP BY kd_biaya");
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Transksi</title>
    <!-- <link rel="stylesheet" type="text/css" href="fontawesome-free/css/all.min.css"> -->
    <!-- <link rel="stylesheet" href="css/transaksi.css">  -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="css/style.css"> <!-- CSS tambahan -->
    <!-- <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script> -->
</head>
<style>
    .custom-modal {
  max-width: 800px;
}
</style>
<body>
<div class="container">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-1">  </div>         
       
        <div class="col-md-2 content-section">
            <div class="alert alert-success">
                <i class="fa fa-university"></i> Identitas Siswa
            </div>
            <form >
                
                <div class="form-group">
                    <label>NIS</label>
                    <input type="text" id="nis2" name="nis2" class="form-control" readonly>
                </div>
                <div class="form-group">
                    <label>Nama</label>
                    <input type="text" id="nama2" name="nama2" class="form-control" readonly>
                </div>
                <div class="form-group">
                    <label>Kelas</label>
                    <input type="text" id="kelas2" name="kelas2"  class="form-control" readonly>
                </div>
                <div class="form-group">
                    <label>Kd Transaksi</label>
                    <input type="text" id="kd_transaksi2" name="kd_transaksi2" class="form-control" readonly>
                </div>
                <div class="form-group">
                    <label>Tgl Transaksi</label>
                    <input type="text" id="tanggal2" name="tanggal2" class="form-control" readonly>
                </div>
            </form>
        </div>


      <div class="col-md-9 content-section">
             <div class="alert alert-success">
                <i class="fa fa-university"></i> Form Transaksi Pembayaran
            </div>
             
            <form action="proses_bayar.php" method="post" id="formTransaksi" onsubmit="return validateForm()">
                <div class="table-responsive">
         <table class="table">
         <thead>
            <tr>
                <td colspan="3" align="right">
                    <h2><strong>Total Bayar</strong></h2>
                </td>
                <td colspan="2" align="right" >
                    <input type="text" id="totalBayar" name="totalBayar" class="form-control"
                        style="width: 250px; height: 60px; font-size: 2rem; font-weight: bold; text-align: right; background-color:rgb(169, 117, 237);" readonly>
                </td>
            </tr>
            <tr align="center" class="table-info" >
                <th>Kode Bayar</th>
                <th>Th Ajaran</th>
                <th>Jumlah</th>
                <th>Jml Bayar</th>
                <th>Kekurangan</th>
                <th>Metode</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody class="after-add-more">
            <tr class="control-group">
                <td>
                    <input type="hidden" class="kd_transaksi form-control" name="kd_transaksi[]" readonly>
                    <input type="text" name="kd_biaya[]" class="form-control" readonly>
                    <input type="hidden" name="user[]" class="form-control user">
                    <input type="hidden" name="tanggal[]" class="form-control tanggal">
                </td>
                <td>
                <input type="text" name="thajaran[]" class="form-control" readonly>
                </td>
                <td>
                    <input type="hidden" name="nis[]" class="id_anggota hidden-after-duplicate" class="form-control" readonly>
                    <input type="text" name="jumlah[]" class="form-control jumlah" readonly>
                </td>
                <td>
                    <input type="hidden" name="nama_anggota[]" class="id_anggota hidden-after-duplicate" class="form-control" readonly>
                    <input type="text" name="bayar[]" class="form-control bayar" autocomplete="off">
                </td>
                <td>
                    <input type="hidden" name="kelas[]" class="id_anggota hidden-after-duplicate" class="form-control" readonly>
                    <input type="text" name="sisa[]" class="form-control sisa" readonly>

                </td>
                <td>
                    <select name="method[]" class="form-control metode" id="metodeBayar">
                        <option value="">Pilih Metode</option>
                        <?php
                            $query = "SELECT DISTINCT method FROM tb_method"; // Ganti nama_tabel sesuai database Anda
                            $result = mysqli_query($conn, $query);
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo '<option value="'.$row['method'].'">'.$row['method'].'</option>';
                        }
                        ?>
                    </select>
                </td>   
                <td class="text-right" >
                    <div class="tombol-aksi d-flex gap-2 justify-content-end">
                        <button type="button" class="btn btn-sm btn-info btn-modal" data-bs-toggle="modal" data-bs-target="#myModal">
                        <i class="fa fa-search"></i>
                    </button>
                
                    <button type="button" class="btn btn-sm btn-danger  remove">
                         <i class="fas fa-trash-alt"></i>
                    </button>
                    </div>
                </td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <!-- Label "Bayar" sejajar dengan "Sisa" -->
                <td colspan="3" align="right">
                    <h4><strong>Bayar</strong></h4>
                </td>
                <!-- Input sejajar dengan "Aksi" -->
                <td colspan="2" align="right">
                    <input type="text" id="inputBayar"  class="form-control" name="inputBayar" autocomplete="off" style="width: 200px; height: 40px; font-size: 2rem; font-weight: bold; text-align: right;">
                </td>
            </tr>
            <tr>
                <!-- Label "Kembali" sejajar dengan "Sisa" -->
                <td colspan="3" align="right">
                    <h4><strong>Kembali</strong></h4>
                </td>
                <!-- Input sejajar dengan "Aksi" -->
                <td colspan="2" align="right">
                    <input type="text" id="inputKembali" name="inputKembali"  class="form-control" style="width: 200px; height: 40px; font-size: 2rem; font-weight: bold; text-align: right; background-color:rgb(137, 123, 155) " readonly>
                </td>
            </tr>
        </tfoot>
    </table>
</div>
        <button class="btn btn-info add-more" type="button">
            <i class="glyphicon glyphicon-plus"></i> Add
        </button>
        <button type="submit" name="submit" class="btn btn-success">
            <i class="glyphicon glyphicon-send"></i> Submit
        </button>
 </form>
</div>

 <!-- sisi kanan -->
 <!-- <div class="col-md-1">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h6>Total Pendapatan Per </h6>
        </div>
        <div class="card-body p-2">
    <p class="mb-0 pb-0"><strong>Hari :</strong> Rp <?= number_format($total_harian, 0, ',', '.') ?></p>
    <p class="mb-0 pb-0"><strong>Bulan:</strong> Rp <?= number_format($total_bulanan, 0, ',', '.') ?></p>
    <p class="mb-0 pb-0"><strong>Tahun:</strong> Rp <?= number_format($total_tahunan, 0, ',', '.') ?></p>
</div>
    </div>

    <div class="card mt-3">
        <div class="card-header bg-info text-white">
            <h6>Total Perkategori</h6>
        </div>
        <div class="card-body">
            <ul class="list-group">
                <?php while ($biaya = mysqli_fetch_assoc($biaya_query)) { ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?= $biaya['kd_biaya'] ?>
                        <span class="badge bg-primary">Rp. <?= number_format($biaya['total'], 0, ',', '.') ?></span>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div> 
</div>   -->

<!-- Modal -->
    <div class="modal fade" id="myModal" role="dialog">
        <div class="modal-dialog  custom-modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Data Biaya Siswa</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;
                   
                    </button>
                </div>
                <div class="modal-body table-responsive">
                    <!-- Textbox pencarian -->
                <input type="text" id="modal-search" class="form-control" placeholder="Cari berdasarkan NIS atau Nama..." style="margin-bottom: 10px;">
                    <table class="table table-bordered table-striped" id="example">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>NIS</th>
                                <th>Nama Siswa</th>
                                <th>Kelas</th>
                                <th>Kd Bayar / Bulan</th>
                                <th>Th Ajaran</th>
                                <th>Jumlah</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="modal-table-body">
                            <tr><td colspan="3" class="text-center">Silakan cari data...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
    
    $(".btn-modal").click(function() {
            var nis = $(this).closest("tr").find(".nama").val();
            $("#modal-search").val(nis).trigger("keyup"); // Isi dan jalankan pencarian otomatis
        });

        // Pencarian real-time saat mengetik di textbox pencarian modal
        $("#modal-search").keyup(function() {
            var searchQuery = $(this).val();
            
            // AJAX untuk mencari data
            $.ajax({
                url: "get_data.php", // Ganti dengan URL skrip PHP pencarian
                type: "POST",
                data: { search: searchQuery },
                success: function(response) {
                    $("#modal-table-body").html(response); // Masukkan hasil pencarian ke tabel
                }
            });
        });

//   ----

    $(".add-more").click(function() { 
    var lastElement = $(".after-add-more .control-group").last();
    var newElement = lastElement.clone();

    // Kosongkan nilai input setelah cloning
    newElement.find("input[name='nis[]']").val("");
    newElement.find("input[name='nama_anggota[]']").val("");
    newElement.find("input[name='kelas[]']").val("");
    newElement.find("input[name='kd_biaya[]']").val("");
    newElement.find("input[name='thajaran[]']").val("");
    newElement.find("input[name='jumlah[]']").val("");
    newElement.find("input[name='bayar[]']").val("");
    newElement.find("input[name='sisa[]']").val("");
     // Pertahankan nilai dari 'kd_transaksi'
    newElement.find("input[name='kd_transaksi']").val(lastElement.find("input[name='kd_transaksi']").val());
    newElement.find("input[name='method']").val(lastElement.find("input[name='method']").val());

    // Pastikan elemen hasil duplikasi tetap hidden sesuai kebutuhan
    newElement.find(".hidden-after-duplicate").hide();

    // Pastikan setiap button modal memiliki ID unik
    var uniqueID = "myModal_" + ($(".after-add-more .control-group").length + 1);
    newElement.find(".btn-modal").attr("data-target", "#" + uniqueID);

    // Duplikasi modal dengan ID unik
    var newModal = $("#myModal").clone().attr("id", uniqueID);
    newModal.find(".modal-title").text("Data Anggota - " + uniqueID);

    // Tambahkan row baru dan modal baru ke dalam dokumen
    $(".after-add-more").append(newElement);
    $("body").append(newModal);
});

    // Saat tombol remove diklik, elemen akan dihapus
    $("body").on("click", ".remove", function() { 
        $(this).closest(".control-group").remove();
    });

    $("#example").DataTable();
});


// ----
$(document).ready(function() {
    generateKodeTransaksi(); // Panggil fungsi saat halaman dimuat

    // Saat tombol dalam tabel diklik, simpan referensi ke barisnya
    $(".after-add-more").on("click", ".btn-modal", function() {
        selectedRow = $(this).closest("tr"); // Simpan baris yang diklik
    });
});

// Fungsi untuk memasukkan data dari modal ke input dalam baris yang diklik
function masuk(el, nis, nama, kelas, kd_biaya, thajaran, jumlah) {
    var modal = $(el).closest(".modal"); // Ambil modal tempat tombol ditekan

    // Isi input yang ada di luar tabel (opsional)
    $("#nis2").val(nis);
    $("#nama2").val(nama);
    $("#kelas2").val(kelas);

    // Pastikan ada baris yang diklik sebelumnya
    if (selectedRow) {
        selectedRow.find(".id_anggota").val(nis);
        selectedRow.find("input[name='nama_anggota[]']").val(nama);
        selectedRow.find("input[name='kelas[]']").val(kelas);
        selectedRow.find("input[name='kd_biaya[]']").val(kd_biaya);
        selectedRow.find("input[name='thajaran[]']").val(thajaran);
        selectedRow.find("input[name='jumlah[]']").val(new Intl.NumberFormat("id-ID").format(jumlah));

    }

    setTimeout(function () {
        modal.modal('hide'); // Tutup modal setelah delay
        $('body').removeClass('modal-open'); // Hapus efek modal terbuka
        $('.modal-backdrop').remove(); // Hapus background modal
    }, 100);
}
function generateKodeTransaksi() {
    let today = new Date();
    let bulan = ('0' + (today.getMonth() + 1)).slice(-2);
    let tahun = today.getFullYear().toString().substr(-2);

    $.ajax({
        url: 'get_last_kode2.php', // Ambil kode terakhir
        type: 'GET',
        data: { bulan: bulan, tahun: tahun },
        success: function(response) {
            let lastNumber = response ? parseInt(response) + 1 : 1; 
            let nomor = ('0000' + lastNumber).slice(-4);
            let kode = `${bulan}.${tahun}.${nomor}`;

            // Masukkan kode transaksi ke semua input dengan class kd_transaksi
            $('.kd_transaksi').val(kode);
            $("#kd_transaksi2").val(kode);
        }
    });
}

$(document).on("input", ".bayar", function() {
    var row = $(this).closest("tr"); // Cari baris terdekat

    // Ambil nilai jumlah dan bayar, hapus format ribuan
    var jumlah = parseFloat(row.find(".jumlah").val().replace(/\D/g, "")) || 0;
    var bayar = parseFloat($(this).val().replace(/\D/g, "")) || 0;
    
    var sisa = jumlah - bayar; // Hitung sisa per baris

    // Tampilkan hasil dengan format ribuan
    row.find(".sisa").val(sisa.toLocaleString("id-ID")); 
});

$(document).ready(function() {
    function formatAngka(angka) {
        return angka.toLocaleString("id-ID"); // Format angka Indonesia (titik ribuan)
    }

    // Fungsi menghitung total bayar
    function hitungTotalBayar() {
        var total = 0;
        $("input[name='bayar[]']").each(function() {
            var bayar = parseInt($(this).val().replace(/\D/g, "")) || 0; // Ambil angka, kalau kosong dianggap 0
            total += bayar;
        });
        $("#totalBayar").val(formatAngka(total)); // Masukkan hasil ke input total_bayar
    }

    $(document).on("input", "input[name='bayar[]']", function() {
    var nilai = $(this).val().replace(/\D/g, ""); // Hanya angka
    
    if (nilai === "") {
        $(this).val(""); // Biarkan kosong jika input dikosongkan
        return;
    }

    // Konversi nilai ke angka besar (hindari parseInt!)
    var angka = Number(nilai); // Bisa menangani angka lebih dari 999999

    // Cek jika angka melebihi batas yang bisa ditampilkan
    if (isNaN(angka)) {
        $(this).val(""); // Jika error, kosongkan input
        return;
    }

    // Format angka dengan ribuan (tanpa membatasi panjang)
    $(this).val(angka.toLocaleString("id-ID"));

    hitungTotalBayar(); // Update total bayar setiap input berubah
});
});

   

    $(document).ready(function() {
    // Format angka untuk jumlah, bayar, dan sisa
    function formatAngka(angka) {
        return angka.toLocaleString("id-ID"); // Format angka Indonesia
    }
});

    // Fungsi untuk update format angka di input jumlah, bayar, sisa
    function updateFormat() {
    $("input[name='jumlah[]'], input[name='sisa[]']").each(function() {
        let nilai = $(this).val().replace(/\./g, ""); // Hapus pemisah ribuan
        nilai = nilai.replace(",", "."); // Kalau ada koma, ganti jadi titik biar bisa diparse
        
        let angka = parseFloat(nilai) || 0; // Konversi ke angka
        $(this).val(formatAngka(angka)); // Format ulang ke angka Indonesia
    });
}

    document.addEventListener("DOMContentLoaded", function () {
    let inputBayar = document.getElementById("inputBayar");
    let inputKembali = document.getElementById("inputKembali");
    let totalBayar = document.getElementById("totalBayar");

    // Fungsi untuk menghapus format angka
    function unformatNumber(value) {
        return parseFloat(value.replace(/\D/g, "")) || 0;
    }

    // Fungsi untuk memformat angka ke format Rupiah
    function formatNumber(value) {
        return new Intl.NumberFormat("id-ID").format(value);
    }

    // Event listener untuk inputBayar agar format angka otomatis
    inputBayar.addEventListener("input", function () {
        let bayar = unformatNumber(this.value);
        this.value = formatNumber(bayar); // Format ulang setelah input

        let total = unformatNumber(totalBayar.value);
        let kembali = bayar - total;

        inputKembali.value = kembali >= 0 ? formatNumber(kembali) : "0";
    });
});

$(document).ready(function () {
    // Ambil username dari PHP
    var username = "<?php echo $_SESSION['user']; ?>";

    function getCurrentDate() {
    var now = new Date();
    var year = now.getFullYear();
    var month = String(now.getMonth() + 1).padStart(2, '0');
    var day = String(now.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}
      // Isi textbox tanggal2 dengan tanggal hari ini
      $("#tanggal2").val(getCurrentDate());


    // Isi tanggal & user ke setiap baris transaksi
    $(".after-add-more tr").each(function () {
        $(this).find(".user").val(username);
        $(this).find(".tanggal").val(getCurrentDate());
       
    });
});

function validateForm() {
    // Ambil nilai yang dipilih dari select dengan id "metodeBayar"
    var metodeBayar = document.getElementById("metodeBayar").value;
    
    // Jika tidak ada yang dipilih (nilai kosong)
    if (metodeBayar === "") {
        // Tampilkan alert
        alert("Pilih metode bayar terlebih dahulu!");
        
        // Cegah form untuk submit
        return false;
    }
    
    // Jika valid, form akan tetap submit
    return true;
}
</script>



</body>
</html>
