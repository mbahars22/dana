<?php
// session_start();
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'koneksi.php';
include  'sidebar.php';

// Pastikan hanya admin yang bisa mengakses halaman ini
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Ambil ID siswa dari parameter URL
$id = isset($_GET['id']) ? $_GET['id'] : '';
if ($id == '') {
    echo "<script>alert('ID siswa tidak valid!'); window.location='admin_dashboard2.php';</script>";
    exit();
}

// Ambil data siswa berdasarkan ID
$query = "SELECT * FROM siswa WHERE id = '$id'";
$result = mysqli_query($conn, $query);
$siswa = mysqli_fetch_assoc($result);

// Jika data siswa tidak ditemukan
if (!$siswa) {
    echo "<script>alert('Data siswa tidak ditemukan!'); window.location='admin_dashboard2.php';</script>";
    exit();
}

// Proses update data saat form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $thajaran = $_POST['thajaran'];
    $nis = $_POST['nis'];
    $nama = $_POST['nama'];
    $t_lahir = $_POST['t_lahir'];
    $tgl_lahir = $_POST['tgl_lahir'];
    $kelas = $_POST['kelas'];
    $alamat = $_POST['alamat'];
    $rombel = $_POST['rombel'];

    $query_update = "UPDATE siswa SET thajaran='$thajaran', nis='$nis', nama='$nama', t_lahir='$t_lahir', tgl_lahir='$tgl_lahir', kelas='$kelas', alamat='$alamat', rombel='$rombel' WHERE id='$id'";

    if (mysqli_query($conn, $query_update)) {
        echo "<script>alert('Data siswa berhasil diperbarui!'); window.location='admin_dashboard2.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui data siswa!');</script>";
    }
}
$kelasResult = mysqli_query($conn, "SELECT DISTINCT kelas FROM mst_kelas ORDER BY kelas ASC");
$kelasList = [];
while ($row = mysqli_fetch_assoc($kelasResult)) {
    $kelasList[] = $row['kelas'];
}


$rombelResult = mysqli_query($conn, "SELECT DISTINCT rombel FROM mst_rombel ORDER BY rombel ASC");
$rombelList = [];
while ($row = mysqli_fetch_assoc($rombelResult)) {
    $rombelList[] = $row['rombel'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Data Siswa</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css"> <!-- CSS tambahan -->
</head>
<body>
    <div class="container mt-5">
        <div class="form-container p-4 shadow-lg rounded bg-white">
            <h2 class="text-center mb-4">Ubah Data Siswa</h2>
            <form action="" method="POST">
                <div class="row">
                    <!-- Kolom 1 -->
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="thajaran" class="form-label">Th Ajaran</label>
                            <input type="text" class="form-control" id="thajaran" name="thajaran" value="<?= $siswa['thajaran'] ?>" required>
                            <label for="nis" class="form-label">NIS</label>
                            <input type="text" class="form-control" id="nis" name="nis" value="<?= $siswa['nis'] ?>" required>
                            
                            <label for="nama" class="form-label mt-2">Nama Lengkap</label>
                            <input type="text" class="form-control" id="nama" name="nama" value="<?= $siswa['nama'] ?>" required>
                            
                            <label for="t_lahir" class="form-label mt-2">Tempat Lahir</label>
                            <input type="text" class="form-control" id="t_lahir" name="t_lahir" value="<?= $siswa['t_lahir'] ?>" required>

                            <label for="tgl_lahir" class="form-label mt-2">Tanggal Lahir </label>
                            <input type="date" class="form-control" id="tgl_lahir" name="tgl_lahir" value="<?= $siswa['tgl_lahir'] ?>" required>

                            <label for="kelas" class="form-label mt-2">Kelas</label>
                            <select name="kelas" class="form-select" required>
    <option value="">-- Pilih Kelas --</option>
    <?php foreach ($kelasList as $kelasOpt) : ?>
        <option value="<?= htmlspecialchars($kelasOpt) ?>" 
            <?= (trim($kelasOpt) == trim($siswa['kelas'])) ? 'selected' : '' ?>>
            <?= htmlspecialchars($kelasOpt) ?>
        </option>
    <?php endforeach; ?>
</select>

                           <label for="rombel" class="form-label mt-2">Rombel</label>
                                <select name="rombel" class="form-select" required>
                                    <option value="">-- Pilih Rombel --</option>
                                    <?php foreach ($rombelList as $rombelOpt) : ?>
                                        <option value="<?= htmlspecialchars($rombelOpt) ?>" <?= ($rombelOpt == $siswa['rombel']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($rombelOpt) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                        </div>
                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea class="form-control" id="alamat" name="alamat" rows="3" required><?= $siswa['alamat'] ?></textarea>
                        </div>
                        
                        <!-- Tombol Aksi -->
                        <button type="submit" class="btn btn-primary w-100">Simpan Perubahan</button>
                        <a href="admin_dashboard2.php" class="btn btn-secondary w-100 mt-2">Kembali</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php include 'admin_footer.php'; ?>
</body>
</html>
