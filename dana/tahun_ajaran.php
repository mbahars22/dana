<?php
include 'koneksi.php';
include 'sidebar.php';

// Tambah tahun ajaran
if (isset($_POST['simpan'])) {
    $tahun_ajaran = $_POST['tahun_ajaran'];
    $query = "INSERT INTO tb_ajaran (th_ajaran) VALUES ('$tahun_ajaran')";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Tahun ajaran berhasil ditambahkan'); window.location.href='tahun_ajaran.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan tahun ajaran');</script>";
    }
}

// Update tahun ajaran
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $tahun_ajaran = $_POST['tahun_ajaran'];
    $query = "UPDATE tb_ajaran SET th_ajaran='$tahun_ajaran' WHERE id=$id";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Tahun ajaran berhasil diperbarui'); window.location.href='tahun_ajaran.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui tahun ajaran');</script>";
    }
}

// Hapus tahun ajaran
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $query = "DELETE FROM tb_ajaran WHERE id=$id";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Tahun ajaran berhasil dihapus'); window.location.href='tahun_ajaran.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus tahun ajaran');</script>";
    }
}

// Tampilkan data
$query_data = mysqli_query($conn, "SELECT * FROM tb_ajaran ORDER BY th_ajaran DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Tahun Ajaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container mt-4">
    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-11">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="m-0">Data Tahun Ajaran</h3>
                <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalTambah">+ Tambah Tahun Ajaran</button>
</div>
                <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Tahun Ajaran</th>
                    <th>Aksi</th>
                </tr>
            </thead>
        <tbody>
            <?php $no = 1; while ($row = mysqli_fetch_assoc($query_data)) : ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= $row['th_ajaran'] ?></td>
                <td>
                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['id'] ?>">
                        <i class="fa fa-edit"></i> Edit
                    </button>
                    <a href="?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">
                        <i class="fa fa-trash"></i> Hapus
                    </a>
                </td>
            </tr>
            <!-- Modal Edit -->
            <div class="modal fade" id="modalEdit<?= $row['id'] ?>" tabindex="-1">
                <div class="modal-dialog">
                    <form method="post">
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Tahun Ajaran</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label>Tahun Ajaran</label>
                                    <input type="text" class="form-control" name="tahun_ajaran" value="<?= $row['th_ajaran'] ?>" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" name="update" class="btn btn-primary">Simpan Perubahan</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <form method="post">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Tahun Ajaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Tahun Ajaran</label>
                        <input type="text" class="form-control" name="tahun_ajaran" placeholder="Contoh: 2024/2025" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="simpan" class="btn btn-success">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html