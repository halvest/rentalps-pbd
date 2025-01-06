<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pembayaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'config/db.php'; ?>
<?php include 'navbar.php'; ?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">Tambah Pembayaran</div>
        <div class="card-body">
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="id_transaksi" class="form-label">ID Transaksi</label>
                    <select name="id_transaksi" id="id_transaksi" class="form-control" required>
                        <option value="">Pilih Transaksi</option>
                        <?php
                        $sql_transaksi = "SELECT t.id_transaksi, p.nama, ps.kategori 
                                          FROM transaksi t
                                          JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
                                          JOIN playstation ps ON t.id_playstation = ps.id_playstation";
                        $result = $conn->query($sql_transaksi);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value='{$row['id_transaksi']}'";
                                if (isset($_POST['id_transaksi']) && $_POST['id_transaksi'] == $row['id_transaksi']) {
                                    echo " selected";
                                }
                                echo ">Transaksi ID {$row['id_transaksi']} - {$row['nama']} ({$row['kategori']})</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="tanggal_bayar" class="form-label">Tanggal Bayar</label>
                    <input type="date" name="tanggal_bayar" id="tanggal_bayar" class="form-control" required value="<?php echo $_POST['tanggal_bayar'] ?? ''; ?>">
                </div>
                <div class="mb-3">
                    <label for="metode_bayar" class="form-label">Metode Bayar</label>
                    <select name="metode_bayar" id="metode_bayar" class="form-control" required>
                        <option value="Cash" <?php echo (isset($_POST['metode_bayar']) && $_POST['metode_bayar'] == 'Cash') ? 'selected' : ''; ?>>Cash</option>
                        <option value="Transfer" <?php echo (isset($_POST['metode_bayar']) && $_POST['metode_bayar'] == 'Transfer') ? 'selected' : ''; ?>>Transfer</option>
                        <option value="E-Wallet" <?php echo (isset($_POST['metode_bayar']) && $_POST['metode_bayar'] == 'E-Wallet') ? 'selected' : ''; ?>>E-Wallet</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="jumlah_bayar" class="form-label">Jumlah Bayar</label>
                    <input type="text" name="jumlah_bayar" id="jumlah_bayar" class="form-control" readonly 
                    value="<?php 
                        if (isset($_POST['id_transaksi'])) {
                            $id_transaksi = $_POST['id_transaksi'];
                            $sql_hitung = "SELECT ps.harga_sewa, DATEDIFF(t.tanggal_kembali, t.tanggal_sewa) AS durasi 
                                           FROM transaksi t 
                                           JOIN playstation ps ON t.id_playstation = ps.id_playstation 
                                           WHERE t.id_transaksi = '$id_transaksi'";
                            $result = $conn->query($sql_hitung);
                            if ($result && $result->num_rows > 0) {
                                $row = $result->fetch_assoc();
                                $harga_sewa = $row['harga_sewa'];
                                $durasi = $row['durasi'];
                                echo $harga_sewa * $durasi;
                            } else {
                                echo 0;
                            }
                        } else {
                            echo 0;
                        }
                    ?>">
                </div>
                <button type="submit" name="calculate" class="btn btn-secondary">Hitung Biaya</button>
                <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>
</div>

<?php
if (isset($_POST['submit'])) {
    $id_transaksi = $_POST['id_transaksi'];
    $tanggal_bayar = $_POST['tanggal_bayar'];
    $metode_bayar = $_POST['metode_bayar'];
    $status = 'Lunas';

    // Hitung Total Pembayaran
    $sql_hitung = "SELECT ps.harga_sewa, DATEDIFF(t.tanggal_kembali, t.tanggal_sewa) AS durasi 
                   FROM transaksi t 
                   JOIN playstation ps ON t.id_playstation = ps.id_playstation 
                   WHERE t.id_transaksi = '$id_transaksi'";
    $result = $conn->query($sql_hitung);
    $row = $result->fetch_assoc();
    $harga_sewa = $row['harga_sewa'];
    $durasi = $row['durasi'];
    $jumlah_bayar = $harga_sewa * $durasi;

    // Simpan ke tabel pembayaran
    $sql_pembayaran = "INSERT INTO pembayaran (id_transaksi, tanggal_bayar, metode_bayar, jumlah_bayar) 
                       VALUES ('$id_transaksi', '$tanggal_bayar', '$metode_bayar', '$jumlah_bayar')";

    if ($conn->query($sql_pembayaran) === TRUE) {
        // Update status ke tabel transaksi
        $sql_update_status = "UPDATE transaksi SET status = '$status' WHERE id_transaksi = '$id_transaksi'";
        if ($conn->query($sql_update_status) === TRUE) {
            echo "<script>alert('Pembayaran berhasil ditambahkan dan status transaksi diperbarui');window.location.href='list_pembayaran.php';</script>";
        } else {
            echo "<script>alert('Pembayaran berhasil, tetapi gagal memperbarui status transaksi');</script>";
        }
    } else {
        echo "<script>alert('Gagal menambahkan pembayaran');</script>";
    }
}
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
