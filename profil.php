<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

include 'koneksi.php';
include 'sidebar.php';

// Ambil data profile (hanya 1 record)
$result = mysqli_query($conn, "SELECT * FROM tb_profile LIMIT 1");
$data = mysqli_fetch_assoc($result);

// Proses simpan/update
if (isset($_POST['ubah'])) {
    $nama   = $_POST['nama_sekolah'];
    $status = $_POST['status'];
    $kepsek = $_POST['kep_sek'];
    $alamat = $_POST['alamat'];
    $kota = $_POST['kota'];

    // Handle upload logo
    $nama_logo = $data['logo'] ?? '';
    if (!empty($_FILES['logo']['name'])) {
        $tmp_name = $_FILES['logo']['tmp_name'];
        $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
        $nama_logo = 'logo_' . time() . '.' . $ext;
        move_uploaded_file($tmp_name, "uploads/" . $nama_logo);
    }

    if ($data) {
        // Jika data sudah ada, lakukan update
        $update = mysqli_query($conn, "UPDATE tb_profile SET 
            nama_sekolah='$nama',
            status='$status',
            kep_sek='$kepsek',
            alamat='$alamat',
            kota='$kota',
            logo='$nama_logo'
            WHERE id=" . $data['id']);
        $pesan = $update ? "Data berhasil diperbarui." : "Gagal memperbarui.";
    }

    // Refresh data setelah update
    $result = mysqli_query($conn, "SELECT * FROM tb_profile LIMIT 1");
    $data = mysqli_fetch_assoc($result);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manajemen Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style3.css">
</head>
<body>
<div class="container mt-2">
    <div class="row">
        <!-- Kolom kiri kosong -->
        <div class="col-md-1"></div>
        <div class="col-11">

            <?php if (!empty($pesan)): ?>
                <div class="alert alert-info"><?php echo $pesan; ?></div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data">
                <div class="mb-2">
                    <label>Nama Sekolah</label>
                    <input type="text" name="nama_sekolah" class="form-control" value="<?php echo $data['nama_sekolah'] ?? ''; ?>" required>
                </div>
                <div class="mb-2">
                    <label>Status</label>
                    <input type="text" name="status" class="form-control" value="<?php echo $data['status'] ?? ''; ?>" required>
                </div>
                <div class="mb-2">
                    <label>Kepala Sekolah</label>
                    <input type="text" name="kep_sek" class="form-control" value="<?php echo $data['kep_sek'] ?? ''; ?>" required>
                </div>
                <div class="mb-2">
                    <label>Alamat</label>
                    <textarea name="alamat" class="form-control" required><?php echo $data['alamat'] ?? ''; ?></textarea>
                </div>
                <div class="mb-2">
                    <label>Kota</label>
                    <textarea name="kota" class="form-control" required><?php echo $data['kota'] ?? ''; ?></textarea>
                </div>
                <div class="mb-2">
                    <label>Logo Sekolah</label>
                    <input type="file" name="logo" class="form-control">
                    <?php if (!empty($data['logo'])): ?>
                        <img src="uploads/<?php echo $data['logo']; ?>" alt="Logo Sekolah" style="height: 80px; margin-top: 10px;">
                    <?php endif; ?>
                </div>
                
                <!-- Tombol Ubah -->
                <button type="submit" name="ubah" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Ubah
                </button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
