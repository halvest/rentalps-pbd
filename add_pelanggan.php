<?php include 'config/db.php'; ?>
<?php include 'navbar.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pelanggan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">Tambah Pelanggan Baru</div>
        <div class="card-body">
            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $nama = trim($_POST['nama']);
                $alamat = trim($_POST['alamat']);
                $email = trim($_POST['email']);
                $no_telepon = trim($_POST['no_telepon']);

                // Validasi data
                if (empty($nama) || empty($alamat) || empty($email) || empty($no_telepon)) {
                    echo '<div class="alert alert-danger">Semua field wajib diisi!</div>';
                } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    echo '<div class="alert alert-danger">Email tidak valid!</div>';
                } else if (!preg_match('/^\d+$/', $no_telepon)) {
                    echo '<div class="alert alert-danger">No telepon hanya boleh berisi angka!</div>';
                } else {
                    // Cek apakah email sudah ada
                    $stmt = $conn->prepare("SELECT * FROM Pelanggan WHERE email = ?");
                    $stmt->bind_param("s", $email);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        echo '<div class="alert alert-danger">Email sudah terdaftar!</div>';
                    } else {
                        // Simpan data
                        $stmt = $conn->prepare("INSERT INTO Pelanggan (nama, alamat, email, no_telepon) VALUES (?, ?, ?, ?)");
                        $stmt->bind_param("ssss", $nama, $alamat, $email, $no_telepon);
                        if ($stmt->execute()) {
                            echo '<div class="alert alert-success">Pelanggan berhasil ditambahkan!</div>';
                        } else {
                            echo '<div class="alert alert-danger">Error: ' . $conn->error . '</div>';
                        }
                    }
                }
            }
            ?>

            <!-- Form Tambah Pelanggan -->
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama</label>
                    <input type="text" class="form-control" id="nama" name="nama" required>
                </div>
                <div class="mb-3">
                    <label for="alamat" class="form-label">Alamat</label>
                    <textarea class="form-control" id="alamat" name="alamat" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="no_telepon" class="form-label">No Telepon</label>
                    <input type="text" class="form-control" id="no_telepon" name="no_telepon" required>
                </div>
                <button type="submit" class="btn btn-success">Tambah</button>
                <a href="pelanggan.php" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
