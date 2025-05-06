<?php 
// session_start();
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'koneksi.php';
include  'sidebar.php';

// Ambil daftar siswa untuk dropdown
$siswa_query = mysqli_query($conn, "SELECT * FROM siswa");
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Biaya Siswa</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-TQ6AU5nQs2mBv1xxoEh0fv8kj8F+eZxFv3LcoGeaRjYbROyrmC+HR90B1o8hzlqfWv0dDJ5VQwQtw80nIYKwVQ==" crossorigin="anonymous" referrerpolicy="no-referrer" /> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css"> <!-- CSS tambahan -->
</head>

<body>
    <div class="container mt-4 ml-3 pt-4">
        <div class="row">
            <div class="col-12">
                <div class="row mb-3">
                    <div class="col-sm-1"> </div>
                    <div class="col-sm-8">
                        <h3 class="m-0">Proses Biaya Pendidikan</h3>
                        
                        <!-- Pastikan form membungkus semua input -->
                        <form method="POST" action="proses_biaya.php">
                            <input type="text" class="form-control" id="nis_text" name="nis_text" placeholder="Ketik Nama atau NIS">
                            <div id="search_results" class="dropdown-menu" style="display: none; width:100%; position: absolute;"></div>
                            <br>
                            <label>NIS:</label>
                            <input type="text" class="form-control" id="nis" name="nis" readonly>
                            <label>Nama:</label>
                            <input type="text" class="form-control" id="nama" name="nama" readonly>
                            <label>Kelas:</label>
                            <input type="text" class="form-control" id="kelas" name="kelas" readonly>
                            <label>Th Ajaran:</label>
                            <input type="text" class="form-control" id="thajaran" name="thajaran" readonly>
                            <br>
                                                <!-- Kontainer Biaya -->
                            <div id="biaya-container">
                                <!-- Data dari get_biaya.php akan muncul di sini -->
                            </div>

                            <br>
                            <button type="submit" class="btn btn-primary btn-lg fw-bold shadow">
                            <i class="fa-solid fa-paper-plane"></i> Kirim Master Biaya
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
        let response = await fetch(`/dana/search_siswa.php?q=${query}`); // FIX PATH
        let data = await response.json();

        resultBox.innerHTML = ""; // Bersihkan hasil lama
        if (data.length > 0) {
            data.forEach(item => {
                let div = document.createElement("div");
                div.classList.add("dropdown-item");
                div.textContent = `${item.nis} - ${item.nama} - ${item.kelas}`;

                div.addEventListener("click", function() {
                    // Masukkan data ke dalam textbox
                    document.getElementById("nis").value = item.nis;
                    document.getElementById("nama").value = item.nama;
                    document.getElementById("kelas").value = item.kelas;
                    document.getElementById("thajaran").value = item.thajaran;
                    // Sembunyikan dropdown
                    resultBox.style.display = "none";

                    if (item.kelas.trim() !== "") {
                        getDataBiaya(item.kelas);
                    }
                });

                resultBox.appendChild(div);
            });
            resultBox.style.display = "block"; // Tampilkan dropdown
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

</body>
