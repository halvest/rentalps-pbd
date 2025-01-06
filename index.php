<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rental PS Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .card-hover:hover {
            transform: scale(1.05);
            transition: all 0.3s ease-in-out;
        }
        .nav-link.active {
            background-color: #0d6efd;
            color: #fff !important;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <?php include 'config/db.php'; ?>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Rental PS</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link active" href="index.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="transaksi.php">Transaksi</a></li>
                    <li class="nav-item"><a class="nav-link" href="pelanggan.php">Pelanggan</a></li>
                    <li class="nav-item"><a class="nav-link" href="pendapatan.php">Laporan Pendapatan</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Dashboard Content -->
    <div class="container mt-4">
        <div class="row">
            <!-- Cards -->
            <?php
            function get_count($table) {
                global $conn;
                $result = $conn->query("SELECT COUNT(*) AS total FROM `$table`");
                $data = $result->fetch_assoc();
                return $data['total'];
            }
            ?>
            <div class="col-md-3">
                <div class="card bg-info text-white card-hover">
                    <div class="card-body text-center">
                        <h5 class="card-title">Admin</h5>
                        <h3><?php echo get_count('Admin'); ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white card-hover">
                    <div class="card-body text-center">
                        <h5 class="card-title">Pelanggan</h5>
                        <h3><?php echo get_count('Pelanggan'); ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white card-hover">
                    <div class="card-body text-center">
                        <h5 class="card-title">PlayStation</h5>
                        <h3><?php echo get_count('Playstation'); ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white card-hover">
                    <div class="card-body text-center">
                        <h5 class="card-title">Transaksi</h5>
                        <h3><?php echo get_count('Transaksi'); ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">Grafik Transaksi</div>
                    <div class="card-body">
                        <canvas id="transaksiChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Data -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-success text-white">Data Pelanggan</div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nama</th>
                                    <th>Alamat</th>
                                    <th>Email</th>
                                    <th>No Telepon</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT * FROM Pelanggan";
                                $result = $conn->query($sql);
                                if ($result->num_rows > 0) {
                                    while($row = $result->fetch_assoc()) {
                                        echo "<tr>
                                                <td>" . $row["id_pelanggan"] . "</td>
                                                <td>" . $row["nama"] . "</td>
                                                <td>" . $row["alamat"] . "</td>
                                                <td>" . $row["email"] . "</td>
                                                <td>" . $row["no_telepon"] . "</td>
                                            </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='5'>No data found</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer mt-5">
        <p>&copy; 2025 Rental PS. All Rights Reserved.</p>
    </div>

    <!-- Chart Script -->
    <script>
        const ctx = document.getElementById('transaksiChart').getContext('2d');
        const transaksiChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['PS1', 'PS2', 'PS3', 'PS4', 'PS5'],
                datasets: [{
                    label: 'Jumlah Transaksi',
                    data: [], // Data will be fetched from the API
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' }
                }
            }
        });

        // Fetch chart data from API
        fetch('api/transaksi_chart_data.php')
            .then(response => response.json())
            .then(data => {
                transaksiChart.data.datasets[0].data = data.transaksi_counts;
                transaksiChart.update();
            })
            .catch(error => console.error('Error fetching chart data:', error));
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>