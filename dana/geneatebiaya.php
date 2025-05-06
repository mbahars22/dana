<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'koneksi.php';
include  'sidebar.php';

$nis = '';
$nis_get = '';
$nama = '';
$kelas = '';
$thajaran = '';
$rombel = '';

if (isset($_GET['nis'])) {
    $nis = $_GET['nis'];
    $nis_get = $nis;

    // Ambil data siswa dari database
    $cek_siswa = mysqli_query($conn, "SELECT * FROM siswa WHERE nis = '$nis'");
    if ($cek_siswa && mysqli_num_rows($cek_siswa) > 0) {
        $data_siswa = mysqli_fetch_assoc($cek_siswa);
        $nama = $data_siswa['nama'] ?? '';
        $kelas = $data_siswa['kelas'] ?? '';
        $thajaran = $data_siswa['thajaran'] ?? '';
        $rombel = $data_siswa['rombel'] ?? '';
    } else {
        echo "<div class='alert alert-danger'>Data siswa tidak ditemukan!</div>";
    }
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Biaya Siswa</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="container mt-4 ml-3 pt-4">
        <div class="row">
            <div class="col-12">
                <div class="row mb-3">
                    <div class="col-sm-1"> </div>
                    <div class="col-sm-8">
                        <h3 class="m-0">Proses Biaya Pendidikan</h3>
                        <form method="POST" action="proses_biaya.php">
                            <input type="text" class="form-control" id="nis_text" name="nis_text" placeholder="Ketik Nama atau NIS" value="<?php echo $nis_get; ?>">
                            <div id="search_results" class="dropdown-menu" style="display: none; width:100%; position: absolute;"></div>
                            <br>
                            <label>NIS:</label>
                            <input type="text" class="form-control" id="nis" name="nis" value="<?php echo $nis_get; ?>" readonly>
                            <label>Nama:</label>
                            <input type="text" class="form-control" id="nama" name="nama" value="<?php echo $nama; ?>" readonly>
                            <label>Kelas:</label>
                            <input type="text" class="form-control" id="kelas" name="kelas" value="<?php echo $kelas; ?>" readonly>
                            <label>rombel:</label>
                            <input type="text" class="form-control" id="rombel" name="rombel" value="<?php echo $rombel; ?>" readonly>
                            <label>Th Ajaran:</label>
                            <input type="text" class="form-control" id="thajaran" name="thajaran" value="<?php echo $thajaran; ?>" readonly>
                            <br>
                            <div id="biaya-container">
                                <!-- Data dari get_biaya.php akan muncul di sini -->
                            </div>
                            <br>
                            <button type="submit" class="btn btn-primary btn-lg fw-bold shadow">
                                <i class="fas fa-save"></i> Simpan Pembayaran
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

<script>
// Live Search
document.getElementById("nis_text").addEventListener("keyup", async function() {
    let query = this.value;
    let resultBox = document.getElementById("search_results");

    if (query.length > 2) {
        let response = await fetch(`/dana/search_siswa.php?q=${query}`);
        let data = await response.json();

        resultBox.innerHTML = "";
        if (data.length > 0) {
            data.forEach(item => {
                let div = document.createElement("div");
                div.classList.add("dropdown-item");
                div.textContent = `${item.nis} - ${item.nama} - ${item.kelas}`;

                div.addEventListener("click", function() {
                    document.getElementById("nis").value = item.nis;
                    document.getElementById("nama").value = item.nama;
                    document.getElementById("kelas").value = item.kelas;
                    document.getElementById("thajaran").value = item.thajaran;
                    resultBox.style.display = "none";

                    if (item.kelas.trim() !== "") {
                        getDataBiaya(item.kelas);
                    }
                });

                resultBox.appendChild(div);
            });
            resultBox.style.display = "block";
        } else {
            resultBox.style.display = "none";
        }
    } else {
        resultBox.style.display = "none";
    } 
});

// Ambil data biaya sesuai kelas
function getDataBiaya(kelas) {
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "get_biaya.php?kelas=" + encodeURIComponent(kelas), true);
    xhr.onload = function () {
        if (this.status == 200) {
            document.getElementById("biaya-container").innerHTML = this.responseText;
        }
    };
    xhr.send();
}
</script>

<?php if (!empty($kelas)) : ?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        getDataBiaya("<?php echo $kelas; ?>");
    });
</script>
<?php endif; ?>

</body>
