<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Transaksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'config/db.php'; ?>
<?php include 'navbar.php'; ?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">Add New Transaksi</div>
        <div class="card-body">
            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $id_pelanggan = $_POST['id_pelanggan'];
                $id_playstation = $_POST['id_playstation'];
                $tanggal_sewa = $_POST['tanggal_sewa'];
                $tanggal_kembali = $_POST['tanggal_kembali'];

                $stmt = $conn->prepare("INSERT INTO Transaksi (id_pelanggan, id_playstation, tanggal_sewa, tanggal_kembali) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("iiss", $id_pelanggan, $id_playstation, $tanggal_sewa, $tanggal_kembali);

                if ($stmt->execute()) {
                    echo "<div class='alert alert-success'>Transaction added successfully.</div>";
                } else {
                    echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
                }
                $stmt->close();
            }
            ?>
            <form method="post" class="form">
                <div class="mb-3">
                    <label for="id_pelanggan" class="form-label">Pelanggan</label>
                    <select class="form-select" id="id_pelanggan" name="id_pelanggan" required>
                        <option value="">Select Pelanggan</option>
                        <?php
                        $pelanggan = $conn->query("SELECT id_pelanggan, nama FROM Pelanggan");
                        while ($row = $pelanggan->fetch_assoc()) {
                            echo "<option value='{$row['id_pelanggan']}'>{$row['nama']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="id_playstation" class="form-label">PlayStation</label>
                    <select class="form-select" id="id_playstation" name="id_playstation" required>
                        <option value="">Select PlayStation</option>
                        <?php
                        $playstation = $conn->query("SELECT id_playstation, kategori FROM Playstation");
                        while ($row = $playstation->fetch_assoc()) {
                            echo "<option value='{$row['id_playstation']}'>{$row['kategori']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="tanggal_sewa" class="form-label">Tanggal Sewa</label>
                    <input type="date" class="form-control" id="tanggal_sewa" name="tanggal_sewa" required>
                </div>
                <div class="mb-3">
                    <label for="tanggal_kembali" class="form-label">Tanggal Kembali</label>
                    <input type="date" class="form-control" id="tanggal_kembali" name="tanggal_kembali" required>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
