<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Pembayaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'config/db.php'; ?>
<?php include 'navbar.php'; ?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">Add Pembayaran</div>
        <div class="card-body">
            <?php
            $jumlah_bayar = '';
            $denda = '';

            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['calculate'])) {
                $id_transaksi = $_POST['id_transaksi'];
                $tanggal_bayar = $_POST['tanggal_bayar'];
                $denda_per_hari = 5000; // Contoh nilai denda per hari

                // Hitung total pembayaran dan denda menggunakan query MySQL
                $sql_hitung = "
                    SELECT ps.harga_sewa, 
                           DATEDIFF(t.tanggal_kembali, t.tanggal_sewa) AS durasi_sewa, 
                           GREATEST(DATEDIFF('$tanggal_bayar', t.tanggal_kembali), 0) AS keterlambatan,
                           (DATEDIFF(t.tanggal_kembali, t.tanggal_sewa) * ps.harga_sewa) + 
                           (GREATEST(DATEDIFF('$tanggal_bayar', t.tanggal_kembali), 0) * $denda_per_hari) AS total_bayar
                    FROM transaksi t 
                    JOIN playstation ps ON t.id_playstation = ps.id_playstation 
                    WHERE t.id_transaksi = '$id_transaksi'
                ";
                $result = $conn->query($sql_hitung);

                if ($result && $result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $jumlah_bayar = $row['total_bayar'];
                    $denda = $row['keterlambatan'] * $denda_per_hari;
                } else {
                    echo "<div class='alert alert-danger'>Transaksi tidak ditemukan atau data tidak valid.</div>";
                }
            }

            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
                $id_transaksi = $_POST['id_transaksi'];
                $tanggal_bayar = $_POST['tanggal_bayar'];
                $metode_bayar = $_POST['metode_bayar'];
                $status = 'Lunas';

                // Simpan ke tabel pembayaran
                $sql_pembayaran = "
                    INSERT INTO pembayaran (id_transaksi, tanggal_bayar, metode_bayar, jumlah_bayar) 
                    VALUES ('$id_transaksi', '$tanggal_bayar', '$metode_bayar', '$jumlah_bayar')
                ";
                if ($conn->query($sql_pembayaran) === TRUE) {
                    // Update status ke tabel transaksi
                    $sql_update_status = "UPDATE transaksi SET status = '$status' WHERE id_transaksi = '$id_transaksi'";
                    if ($conn->query($sql_update_status) === TRUE) {
                        echo "<div class='alert alert-success'>Pembayaran berhasil ditambahkan dan status transaksi diperbarui.</div>";
                    } else {
                        echo "<div class='alert alert-warning'>Pembayaran berhasil, tetapi gagal memperbarui status transaksi.</div>";
                    }
                } else {
                    echo "<div class='alert alert-danger'>Gagal menambahkan pembayaran.</div>";
                }
            }
            ?>

            <form method="post" class="form">
                <div class="mb-3">
                    <label for="id_transaksi" class="form-label">Transaction ID</label>
                    <select class="form-select" id="id_transaksi" name="id_transaksi" required>
                        <option value="">Select Transaction</option>
                        <?php
                        $transaksi = $conn->query("SELECT id_transaksi, id_pelanggan FROM transaksi WHERE status != 'Lunas'");
                        while ($row = $transaksi->fetch_assoc()) {
                            $selected = isset($_POST['id_transaksi']) && $_POST['id_transaksi'] == $row['id_transaksi'] ? 'selected' : '';
                            echo "<option value='{$row['id_transaksi']}' $selected>ID {$row['id_transaksi']} - Pelanggan {$row['id_pelanggan']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="tanggal_bayar" class="form-label">Tanggal Pembayaran</label>
                    <input type="date" class="form-control" id="tanggal_bayar" name="tanggal_bayar" value="<?= isset($_POST['tanggal_bayar']) ? $_POST['tanggal_bayar'] : '' ?>" required>
                </div>
                <button type="submit" name="calculate" class="btn btn-secondary mb-3">Hitung Total</button>

                <div class="mb-3">
                    <label for="metode_bayar" class="form-label">Metode Pembayaran</label>
                    <select class="form-select" id="metode_bayar" name="metode_bayar" required>
                        <option value="">Select Payment Method</option>
                        <option value="Cash" <?= isset($_POST['metode_bayar']) && $_POST['metode_bayar'] == 'Cash' ? 'selected' : '' ?>>Cash</option>
                        <option value="Transfer" <?= isset($_POST['metode_bayar']) && $_POST['metode_bayar'] == 'Transfer' ? 'selected' : '' ?>>Transfer</option>
                        <option value="E-Wallet" <?= isset($_POST['metode_bayar']) && $_POST['metode_bayar'] == 'E-Wallet' ? 'selected' : '' ?>>E-Wallet</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="total_bayar" class="form-label">Total Pembayaran</label>
                    <input type="text" class="form-control" id="total_bayar" name="total_bayar" value="<?= htmlspecialchars($jumlah_bayar) ?>" readonly>
                </div>
                <div class="mb-3">
                    <label for="denda" class="form-label">Denda (Jika Ada)</label>
                    <input type="text" class="form-control" id="denda" name="denda" value="<?= htmlspecialchars($denda) ?>" readonly>
                </div>
                <button type="submit" name="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
