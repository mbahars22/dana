<?php
include 'koneksi.php'; // Pastikan file koneksi database tersedia
include  'sidebar.php';
// Ambil data dari tabel jenis_transaksi
// $query = "SELECT * FROM jenis_transaksi";
// $result = mysqli_query($conn, $query);

// Ambil jumlah data per halaman dari dropdown, default 10
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Ambil total jumlah siswa untuk pagination
$query_total = "SELECT COUNT(*) as total FROM jenis_transaksi";
$result_total = mysqli_query($conn, $query_total);
$total_jenis = mysqli_fetch_assoc($result_total)['total'];
$total_pages = ceil($total_jenis / $limit);

// Ambil data jenis sesuai halaman dan limit
$query_jenis = "SELECT * FROM jenis_transaksi ORDER BY id ASC LIMIT $limit OFFSET $offset";
$result_jenis = mysqli_query($conn, $query_jenis);

// Cek apakah query berhasil
if (!$result_jenis) {
    die("Query Error: " . mysqli_error($conn));
}

$kelasResult = mysqli_query($conn, "SELECT DISTINCT kelas FROM mst_kelas ORDER BY kelas ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Master Jenis Biaya</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-TQ6AU5nQs2mBv1xxoEh0fv8kj8F+eZxFv3LcoGeaRjYbROyrmC+HR90B1o8hzlqfWv0dDJ5VQwQtw80nIYKwVQ==" crossorigin="anonymous" referrerpolicy="no-referrer" /> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css"> <!-- CSS tambahan -->
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
                    <div class="col-md-6 text-end"> 
                    <a href="downl_temp_jen_trans.php" class="btn btn-success">
                        <i class="fa fa-download"></i> Template Excel
                    </a>
                                            <button class="btn btn-primary mb-1" data-bs-toggle="modal" data-bs-target="#modalTambah">+Tambah Jenis Biaya</button>
                        <button class="btn btn-success mb-1" data-bs-toggle="modal" data-bs-target="#modalImport">
                            📂 Impor Excel
                        </button>
                        </div>
                </div>

    <table class="table table-bordered text-center">
    <thead class="table-dark">
        <tr>
            <th>No</th> <!-- Ganti ID dengan No -->
            <th>Kode Biaya</th>
            <th>Volume</th>
            <th>Kd Kelas</th>
             <th>Jumlah</th>
            <th>Th Ajaran</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $no = $offset + 1; // Inisialisasi nomor urut sesuai halaman
        while ($row = mysqli_fetch_assoc($result_jenis)) { ?>
            <tr>
                <td><?= $no++; ?></td> <!-- Gunakan nomor urut -->
                <td><?= $row['kd_biaya']; ?></td>
                <td><?= $row['volume']; ?></td>
                <td><?= $row['kelas']; ?></td>
                 <td>Rp. <?= number_format($row['jumlah'], 0, ',', '.'); ?></td>
                <td><?= $row['th_ajaran']; ?></td>
                <td>
                    <button class='btn btn-warning btn-sm btn-edit' 
                        data-id="<?= $row['id'] ?>" 
                        data-kd="<?= $row['kd_biaya'] ?>" 
                        data-volume="<?= $row['volume'] ?>" 
                        data-kelas="<?= $row['kelas'] ?>" 
                        data-jumlah="<?= $row['jumlah'] ?>"
                        data-th_ajaran="<?= $row['th_ajaran'] ?>">
                        Edit
                    </button>
                    <button class="btn btn-danger btn-sm btn-delete" data-id="<?= $row['id']; ?>">Hapus</button>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>
<!-- Pagination & Select Limit -->
            <div class="row align-items-center">
                    <div class="col-md-6 ml-4">
                        <label class="form-label me-2">Tampilkan</label>
                        <select id="limitSelect" class="form-select form-select-sm w-auto d-inline" onchange="updateLimit()">
                            <option value="10" <?= ($limit == 10) ? 'selected' : '' ?>>10</option>
                            <option value="20" <?= ($limit == 20) ? 'selected' : '' ?>>20</option>
                            <option value="50" <?= ($limit == 50) ? 'selected' : '' ?>>50</option>
                        </select>
                        <label class="form-label ms-2">data per halaman</label>
                    </div>
                    <div class="col-md-6">
                        <nav class="d-flex justify-content-end">
                            <ul class="pagination m-0">
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?limit=<?= $limit ?>&page=<?= $page - 1 ?>">Previous</a>
                                    </li>
                                <?php endif; ?>

                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                                        <a class="page-link" href="?limit=<?= $limit ?>&page=<?= $i ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>

                                <?php if ($page < $total_pages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?limit=<?= $limit ?>&page=<?= $page + 1 ?>">Next</a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    </div>
                </div>
        
<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahLabel">Tambah Jenis Biaya</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="jenis_biaya_tambah.php" method="post" id="formInput">
                <div class="modal-body">
                    <div class="mb-3">
                    <label>Kode Biaya</label>
                        <input type="text" name="kd_biaya" id="kd_biaya" class="form-control" required maxlength="6" autocomplete="off">
                        <div class="invalid-feedback">
                            Kode biaya harus diawali dengan 3 huruf. Contoh: SPP_01
                        </div>
                        <ul id="list_kd_biaya" class="list-group" style="position:absolute; z-index:1000;"></ul>
                    </div>
                    <div class="mb-3">
                        <label>Volume</label>
                        <input type="text" name="volume" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                    <label>KD KELAS</label>
                        <select name="kelas" class="form-select" required>
                            <option value="">-- Pilih Kelas --</option>
                            <?php while ($row = mysqli_fetch_assoc($kelasResult)) : ?>
                                <option value="<?= htmlspecialchars($row['kelas']) ?>"><?= htmlspecialchars($row['kelas']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                   
                    <div class="mb-3">
                        <label>Jumlah</label>
                        <input type="text" name="jumlah" class="form-control format-rupiah" required>
                    </div>
                    <div class="mb-3">
                        <label>Tahun Ajaran</label>
                        <input type="text" name="th_ajaran" class="form-control" maxlength="5" placeholder="contoh : 24/25" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditLabel">Edit Jenis Biaya</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formEdit">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="mb-3">
                        <label>Kode Biaya</label>
                        <input type="text" class="form-control" id="edit_kd_biaya" name="kd_biaya" readonly>
                    </div>
                    <div class="mb-3">
                        <label>Volume</label>
                        <input type="text" class="form-control" id="edit_volume" name="volume" required>
                    </div>
                    <div class="mb-3">
                        <label>Kd Kelas</label>
                        <input type="text" class="form-control" id="edit_kelas" name="kelas" required>
                    </div>
                   
                    <div class="mb-3">
                        <label>Jumlah</label>
                        <input type="text" class="form-control" id="edit_jumlah" name="jumlah" required>
                    </div>
                    <div class="mb-3">
                        <label>Th Ajaran</label>
                        <input type="text" class="form-control" id="edit_th_ajaran" name="th_ajaran" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmDeleteLabel">Konfirmasi Hapus</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Apakah Anda yakin ingin menghapus data ini?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" id="confirmDelete">Ya, Hapus</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Impor Excel (Dibawah Modal Detail Data) -->
<div class="modal fade" id="modalImport" tabindex="-1" aria-labelledby="modalImportLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalImportLabel">Impor Data Siswa dari Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="impor_biaya.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="fileExcel" class="form-label">Pilih File Excel</label>
                        <input type="file" class="form-control" name="fileExcel" id="fileExcel" accept=".xls, .xlsx" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Upload & Impor</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Format input jumlah menjadi Rupiah
    $(document).on("keyup", ".format-rupiah", function() {
        let angka = $(this).val().replace(/\D/g, "");
        $(this).val(new Intl.NumberFormat("id-ID").format(angka));
    });

    
</script>



<script>
$(document).ready(function() {
    // Delegasikan event ke tbody agar tetap bisa menangkap event klik setelah pagination
    $("body").on("click", ".btn-edit", function() {
    let id = $(this).data("id");
    let kd_biaya = $(this).data("kd");
    let volume = $(this).data("volume");
    let kelas = $(this).data("kelas");
    let jumlah = $(this).data("jumlah");
    let th_ajaran = $(this).data("th_ajaran");

    // Masukkan data ke dalam modal
    $("#edit_id").val(id);
    $("#edit_kd_biaya").val(kd_biaya);
    $("#edit_volume").val(volume);
    $("#edit_kelas").val(kelas);  // Tambahkan ini
    $("#edit_jumlah").val(jumlah);
    $("#edit_th_ajaran").val(th_ajaran);  // Tambahkan ini

    // Tampilkan modal
    $("#modalEdit").modal("show");
});
});

// Proses submit form edit dengan AJAX
$("#formEdit").submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: "edit_biaya.php", // File PHP untuk update data
            type: "POST",
            data: $(this).serialize(),
            success: function(response) {
                alert("Data berhasil diupdate!");
                location.reload(); // Refresh halaman setelah edit
            },
            error: function() {
                alert("Terjadi kesalahan, coba lagi!");
            }
        });
    });

</script>

<!-- deleta -->
<script>
$(document).on("click", ".btn-delete", function() {
    var id = $(this).data("id");
    $("#confirmDelete").data("id", id);
    $("#confirmDeleteModal").modal("show");
});

$("#confirmDelete").on("click", function() {
    var id = $(this).data("id");
    $.ajax({
        url: "jenis_delete.php",
        type: "POST",
        data: { id: id },
        success: function(response) {
            if (response.trim() == "success") {
                alert("Jenis biaya berhasil dihapus!");
                location.reload();
            } else {
                alert("Gagal menghapus jenis biaya!");
            }
        }
    });
    $("#confirmDeleteModal").modal("hide");
});

$(document).ready(function() {
    $('#kd_biaya').on('input', function () {
        var kode = $(this).val();
        var regex = /^[A-Za-z]{3}/;

        if (!regex.test(kode)) {
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid');
        }
    });

    // Cegah submit kalau kode tidak valid
    $('#formInput').on('submit', function (e) {
        var kode = $('#kd_biaya').val();
        var regex = /^[A-Za-z]{3}/;

        if (!regex.test(kode)) {
            $('#kd_biaya').addClass('is-invalid');
            e.preventDefault(); // cegah form dikirim
        }
    });
});
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#kd_biaya').on('keyup', function() {
        let query = $(this).val();
        if (query.length >= 1) {
            $.ajax({
                url: 'ambil_kd_biaya.php',
                method: 'GET',
                data: { term: query },
                success: function(data) {
                    let result = JSON.parse(data);
                    let list = '';
                    result.forEach(function(item) {
                        list += `<li class="list-group-item list-group-item-action" style="cursor:pointer">${item}</li>`;
                    });
                    $('#list_kd_biaya').html(list).show();
                }
            });
        } else {
            $('#list_kd_biaya').hide();
        }
    });

    $(document).on('click', '#list_kd_biaya li', function() {
        let selected = $(this).text();
        $('#kd_biaya').val(selected + '_');
        $('#list_kd_biaya').hide();
    });

    $(document).click(function(e) {
        if (!$(e.target).closest('#kd_biaya, #list_kd_biaya').length) {
            $('#list_kd_biaya').hide();
        }
    });
});
</script>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
