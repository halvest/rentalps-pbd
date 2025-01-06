<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pendapatan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'config/db.php'; ?>
<?php include 'navbar.php'; ?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            Laporan Pendapatan
        </div>
        <div class="card-body">
            <!-- Form Filter Tanggal -->
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-5">
                    <label for="start_date" class="form-label">Dari Tanggal</label>
                    <input type="date" id="start_date" name="start_date" class="form-control" value="<?= isset($_GET['start_date']) ? $_GET['start_date'] : '' ?>">
                </div>
                <div class="col-md-5">
                    <label for="end_date" class="form-label">Sampai Tanggal</label>
                    <input type="date" id="end_date" name="end_date" class="form-control" value="<?= isset($_GET['end_date']) ? $_GET['end_date'] : '' ?>">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>

            <!-- Tabel Laporan Pendapatan -->
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Bulan</th>
                        <th>Total Pendapatan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Ambil filter tanggal dari form
                    $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
                    $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

                    // Query dengan filter tanggal jika ada
                    $sql = "SELECT MONTHNAME(tanggal_bayar) AS bulan, SUM(jumlah_bayar) AS total_pendapatan 
                            FROM pembayaran";
                    
                    if (!empty($start_date) && !empty($end_date)) {
                        $sql .= " WHERE tanggal_bayar BETWEEN '$start_date' AND '$end_date'";
                    }
                    
                    $sql .= " GROUP BY MONTH(tanggal_bayar) 
                              ORDER BY MONTH(tanggal_bayar)";

                    $result = $conn->query($sql);

                    // Cek jika ada hasil
                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            // Highlight bulan saat ini
                            $current_month = date('F');
                            $highlight = ($row['bulan'] === $current_month) ? 'class="table-success"' : '';

                            echo "<tr $highlight>
                                    <td>{$row['bulan']}</td>
                                    <td>Rp " . number_format($row['total_pendapatan'], 0, ',', '.') . "</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='2' class='text-center'>Tidak ada data untuk ditampilkan</td></tr>";
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
