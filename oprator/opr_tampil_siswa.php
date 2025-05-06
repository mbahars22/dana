<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include '../koneksi.php';
include 'opr_sidebar.php';

// Pastikan hanya admin yang bisa mengakses halaman ini
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'operator') {
    header("Location: index.php");
    exit();
}

// Ambil tahun ajaran dari session login
$thajaran = isset($_SESSION['thajaran']) ? $_SESSION['thajaran'] : '';

// Ambil jumlah data per halaman dari dropdown, default 10
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Ambil total jumlah siswa untuk pagination berdasarkan tahun ajaran
$query_total = "SELECT COUNT(*) as total FROM siswa WHERE thajaran = ?";
$stmt_total = $conn->prepare($query_total);
$stmt_total->bind_param("s", $thajaran);
$stmt_total->execute();
$result_total = $stmt_total->get_result();
$total_siswa = $result_total->fetch_assoc()['total'];
$total_pages = ceil($total_siswa / $limit);

// Ambil data siswa sesuai tahun ajaran, halaman, dan limit
$query_siswa = "SELECT * FROM siswa WHERE thajaran = ? ORDER BY id DESC LIMIT ? OFFSET ?";
$stmt_siswa = $conn->prepare($query_siswa);
$stmt_siswa->bind_param("sii", $thajaran, $limit, $offset);
$stmt_siswa->execute();
$result_siswa = $stmt_siswa->get_result();

// Ambil daftar tahun ajaran dari tabel siswa untuk select
$query_thajaran = "SELECT DISTINCT thajaran FROM siswa";
$stmt_thajaran = $conn->prepare($query_thajaran);
$stmt_thajaran->execute();
$result_thajaran = $stmt_thajaran->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Data Siswa</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style3.css"> <!-- CSS tambahan -->
</head>
<body>
    <div class="container mt-2">
        <div class="row">
            <!-- Kolom kiri kosong -->
            <div class="col-md-1"></div>

            <div class="col-11">
                <!-- Baris judul dan tombol tambah siswa -->
                <div class="row align-items-center mb-3">
                    <div class="col-md-4">
                        <h3 class="m-0">Daftar Siswa <?= htmlspecialchars($thajaran) ?></h3>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group shadow-sm">
                            <input type="text" id="searchInput" class="form-control border-primary" placeholder="Cari NIS, Nama, atau Kelas...">
                            <span class="input-group-text bg-primary text-white">
                                <i class="fas fa-search"></i>
                            </span>
                        </div>
                    </div>
                   
                    
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr align="center">
                                <th width="5%">No</th>
                                <th width="7%">NIS</th>
                                <th width="18%">Nama</th>
                                <th width="5%">Kelas</th>
                                <th width="20%">Alamat</th>
                                <th width="5%">Rombel</th>
                                <th width="40%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="siswaTable">
                            <?php
                            $no = $offset + 1;
                            if ($result_siswa->num_rows > 0) {
                                while ($row = $result_siswa->fetch_assoc()) {
                                    echo "<tr>
                                        <td>{$no}</td>
                                        <td>{$row['nis']}</td>
                                        <td>{$row['nama']}</td>
                                        <td>{$row['kelas']}</td>
                                        <td>{$row['alamat']}</td>
                                        <td>{$row['rombel']}</td>
                                        <td align='center'>
                                          
                                            <button class='btn btn-info btn-sm btn-detail'
                                                data-nis='{$row['nis']}'
                                                data-bs-toggle='modal'
                                                data-bs-target='#modalDetailBiaya'>
                                                Detail Biaya
                                            </button>
                                            
                                            <button class='btn btn-warning btn-sm btn-cekbiaya'
                                                data-nis='{$row['nis']}'
                                                data-bs-toggle='modal'
                                                data-bs-target='#modalCekPembayaran'>
                                                Cek Pembayaran
                                            </button>
                                        </td>
                                    </tr>";
                                    $no++;
                                }
                            } else {
                                echo "<tr><td colspan='6' class='text-center'>Tidak ada data siswa untuk tahun ajaran ini.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <!-- Dropdown Limit -->
    <div>
        <select id="limitSelect" class="form-select border-primary shadow-sm" style="width: auto;">
            <?php
            $options = [10, 20, 50, 100];
            foreach ($options as $opt) {
                $selected = ($limit == $opt) ? 'selected' : '';
                echo "<option value='$opt' $selected>$opt / halaman</option>";
            }
            ?>
        </select>
    </div>

                        <!-- Pagination -->
                        <nav>
                            <ul class="pagination mb-0">
                                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?limit=<?= $limit ?>&page=<?= $page - 1 ?>">Previous</a>
                                </li>
                                <li class="page-item disabled">
                                    <span class="page-link">Halaman <?= $page ?> dari <?= $total_pages ?></span>
                                </li>
                                <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?limit=<?= $limit ?>&page=<?= $page + 1 ?>">Next</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        
<!-- modal detail biaya -->
<div class="modal fade" id="modalDetailBiaya" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detail Biaya Siswa</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="isiDetailBiaya">
        <!-- Isi detail dari AJAX akan masuk di sini -->
      </div>
    </div>
  </div>
</div>




<!-- modal cek biaya per siswa  -->
 <!-- Modal -->
 <div class="modal fade" id="modalCekPembayaran" tabindex="-1" role="dialog" aria-labelledby="cekPembayaranLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Cek Sinkronisasi Pembayaran</h5>
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tutup</button> <!-- Tombol Tutup -->
      </div>
      <div class="modal-body row">
        <div class="col-md-4">
          <h6>Data Biaya Siswa</h6>
          <div id="dataBiaya"></div>
        </div>
        <div class="col-md-8">
          <h6>Data Pembayaran Siswa</h6>
          <div id="dataPembayaran"></div>
        </div>
      </div>
      <div class="modal-footer">
       
      </div>

    </div>
  </div>
</div>
        <!-- Modal Hapus Kolektif -->
        <div class="modal fade" id="modalHapusKolektif" tabindex="-1" aria-labelledby="modalHapusKolektifLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Pilih Tahun Ajaran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formHapusKolektif" method="POST" action="hapus_kolektif.php">
                            <div class="form-group">
                                <label for="thajaran">Tahun Ajaran</label>
                                <select class="form-control" id="thajaran" name="thajaran">
                                    <?php while ($row_thajaran = $result_thajaran->fetch_assoc()) { ?>
                                        <option value="<?= $row_thajaran['thajaran'] ?>"><?= $row_thajaran['thajaran'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-danger mt-3">Hapus Semua Siswa Tahun Ajaran Tersebut</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- jQuery -->
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).on('click', '.btn-detail', function () {
    var nis = $(this).data('nis');
    
    $.ajax({
        url: '../get_detail_biaya.php',
        type: 'GET',
        data: { nis: nis },
        success: function (response) {
            $('#isiDetailBiaya').html(response); // isi kontennya ke modal
        },
        error: function () {
            $('#isiDetailBiaya').html('<p class="text-danger">Gagal mengambil data!</p>');
        }
    });
});

// cek biaya individu
$(document).ready(function(){
    $('#modalCekPembayaran').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget);
      var nis = button.data('nis');
      
      $('#dataBiaya').load('../cek_biaya.php?nis=' + nis);
      $('#dataPembayaran').load('../cek_pembayaran.php?nis=' + nis);
    });
  });

//   live search siswa 
  $('#searchInput').on('keyup', function () {
    var keyword = $(this).val();

    $.ajax({
      url: 'opr_live_search_siswa.php',
      method: 'POST',
      data: { keyword: keyword },
      success: function (data) {
        $('#siswaTable').html(data);
      }
    });
  });

document.getElementById('limitSelect').addEventListener('change', function () {
    const selectedLimit = this.value;
    window.location.href = "?limit=" + selectedLimit + "&page=1";
});

$(document).ready(function() {
  $('.btn-delete').click(function() {
    let id = $(this).data('id');

    if(confirm('Yakin mau hapus data ini?')) {
      $.ajax({
        url: 'siswa_delete.php', // ganti sesuai file proses hapus
        type: 'POST',
        data: { id: id },
        success: function(response) {
          alert('Data berhasil dihapus!');
          location.reload(); // reload halaman biar data terbaru muncul
        },
        error: function(xhr, status, error) {
          alert('Terjadi kesalahan: ' + error);
        }
      });
    }
  });
});

</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>



</body>
</html>
