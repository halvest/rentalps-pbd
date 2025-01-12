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
                $tanggal_sewa = $_POST['tanggal_sewa'];
                $tanggal_kembali = $_POST['tanggal_kembali'];
                $id_playstation_array = $_POST['id_playstation'] ?? []; // Checkbox array
                $status = "Pending";

                // Validasi tanggal
                $errors = [];

                if (empty($tanggal_sewa) || empty($tanggal_kembali)) {
                    $errors[] = "Tanggal sewa dan tanggal kembali harus diisi.";
                } elseif (strtotime($tanggal_kembali) < strtotime($tanggal_sewa)) {
                    $errors[] = "Tanggal kembali tidak boleh lebih awal dari tanggal sewa.";
                }

                if (empty($id_playstation_array)) {
                    $errors[] = "Harap pilih setidaknya satu PlayStation.";
                }

                if (!empty($errors)) {
                    foreach ($errors as $error) {
                        echo "<div class='alert alert-danger'>$error</div>";
                    }
                } else {
                    // Query untuk memasukkan data ke tabel transaksi
                    $query = "INSERT INTO transaksi (id_pelanggan, tanggal_sewa, tanggal_kembali, status) VALUES (?, ?, ?, ?)";
                    $stmt = $conn->prepare($query);

                    if (!$stmt) {
                        echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
                    } else {
                        $stmt->bind_param("isss", $id_pelanggan, $tanggal_sewa, $tanggal_kembali, $status);

                        if ($stmt->execute()) {
                            $id_transaksi = $stmt->insert_id;

                            // Query untuk memasukkan data ke tabel detail_transaksi
                            foreach ($id_playstation_array as $id_playstation) {
                                $query_detail = "INSERT INTO transaksi (id_transaksi, id_playstation) VALUES (?, ?)";
                                $stmt_detail = $conn->prepare($query_detail);

                                if (!$stmt_detail) {
                                    echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
                                } else {
                                    $stmt_detail->bind_param("ii", $id_transaksi, $id_playstation);
                                    $stmt_detail->execute();
                                    $stmt_detail->close();
                                }
                            }

                            echo "<div class='alert alert-success'>Transaction added successfully.</div>";
                        } else {
                            echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
                        }
                        $stmt->close();
                    }
                }
            }
            ?>

            <form method="post" class="form">
                <div class="mb-3">
                    <label for="id_pelanggan" class="form-label">Pelanggan</label>
                    <select class="form-select" id="id_pelanggan" name="id_pelanggan" required>
                        <option value="">Select Pelanggan</option>
                        <?php
                        $pelanggan = $conn->query("SELECT id_pelanggan, nama FROM pelanggan");
                        while ($row = $pelanggan->fetch_assoc()) {
                            echo "<option value='{$row['id_pelanggan']}'>{$row['nama']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="id_playstation" class="form-label">PlayStation</label>
                    <?php
                    $playstation = $conn->query("SELECT id_playstation, kategori FROM playstation");
                    while ($row = $playstation->fetch_assoc()) {
                        echo "<div class='form-check'>
                                <input class='form-check-input' type='checkbox' id='ps-{$row['id_playstation']}' name='id_playstation[]' value='{$row['id_playstation']}'>
                                <label class='form-check-label' for='ps-{$row['id_playstation']}'>{$row['kategori']}</label>
                              </div>";
                    }
                    ?>
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
