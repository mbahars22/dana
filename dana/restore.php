<!DOCTYPE html>
<html>
<?php include 'sidebar.php'; ?>
<head>
    <title>Upload & Restore Database</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- CSS tambahan -->
    <link rel="stylesheet" href="css/style.css"> 
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="container mt-4">
    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-11">
            <div class="row align-items-center mb-3">
                <div class="col-md-6">
                    <h2>Upload File Backup (.sql) dan Restore</h2>
                    <form action="proses_restore.php" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="sql_file" class="form-label">Pilih file backup (*.sql):</label>
                            <input type="file" class="form-control" name="sql_file" id="sql_file" accept=".sql" required>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload"></i> Upload & Restore
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
