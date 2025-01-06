<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'config/db.php'; ?>
<?php include 'navbar.php'; ?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">Data Transaksi</div>
        <div class="card-body">
            <a href="add_transaksi.php" class="btn btn-success mb-3">Add New Transaksi</a>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID Transaksi</th>
                        <th>Nama Pelanggan</th>
                        <th>Kategori PS</th>
                        <th>Tanggal Sewa</th>
                        <th>Tanggal Kembali</th>
                        <th>Status Pemmbayaran</th>
                        <th>Status Pengembalian</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT t.*, p.nama AS pelanggan_nama, ps.kategori AS playstation_kategori 
                            FROM Transaksi t
                            JOIN Pelanggan p ON t.id_pelanggan = p.id_pelanggan
                            JOIN Playstation ps ON t.id_playstation = ps.id_playstation
                            ORDER BY t.tanggal_sewa";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                <td>{$row['id_transaksi']}</td>
                                <td>{$row['pelanggan_nama']}</td>
                                <td>{$row['playstation_kategori']}</td>
                                <td>{$row['tanggal_sewa']}</td>
                                <td>{$row['tanggal_kembali']}</td>
                                <td>{$row['status']}</td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No transactions found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
