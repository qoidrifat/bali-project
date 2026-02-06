<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="styles/hasil.bus.css" />
    <title>Hasil Pencarian</title>
    <style>
        .nav-item {
            font-family: "Poppins", sans-serif;
            font-weight: 400;
            font-size: 20px;
            color: #000000;
            cursor: pointer;
            position: relative;
            padding: 5px 0;
        }

        .nav-item::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 3px;
            background-color: #000000;
            transition: width 0.3s;
        }

        .nav-item:hover {
            font-weight: 700;
        }

        .nav-item:hover::after {
            width: 100%;
        }

        .nav-item.active {
            font-family: "Poppins", sans-serif;
            font-weight: 700;
            position: relative;
        }

        .nav-item.active::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 100%;
            height: 3px;
            background-color: #000000;
        }

        .results {
            display: inline-block;
            justify-content: center;
            align-items: center;
            margin-left: 10%;
        }

        table {
            border-collapse: collapse;
            margin: 20px 0;
        }

        th,
        td {
            text-align: center;
            padding: 12px;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        .header {
            align-items: center;
        }

        .back-button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 30px;
        }

        .back-button:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>
    <div class="navbar">
        <div class="navbar_container">
            <div class="brand">
                <img src="images/logo.png" alt="logo" />
            </div>
            <div class="menu">
                <a class="nav-item" href="index.php">Home</a>
                <a class="nav-item" href="destination.php">Destination</a>
                <a class="nav-item" href="about.php">About</a>
                <a class="nav-item" href="contact.php">Contact</a>
                <a class="nav-item" href="visa.php">Visa</a>
                <a class="nav-item active" href="transport.php">Transport</a>
                <a class="nav-item" href="tiket.php">Tiket</a>
            </div>
        </div>
    </div>
    <div class="results">
        <div class="main-content">
            <div class="header">
                <a class="back-button" href="transport.php">Kembali</a>
                <h1>Hasil Pencarian Tiket Pesawat</h1>
            </div>
            <?php
            // Database connection
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "bali";

            // Create connection
            $conn = new mysqli($servername, $username, $password, $dbname);

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Fetching form data
            $from = $_GET['from'];
            $to = $_GET['to'];
            $departureDate = $_GET['departure-date'];
            $returnDate = isset($_GET['return-date']) ? $_GET['return-date'] : null;
            $passengers = $_GET['passengers'];

            // Mapping destination IDs to names
            $from_name = '';
            if ($from == '1') {
                $from_name = 'Surabaya';
            } elseif ($from == '2') {
                $from_name = 'Denpasar';
            } else {
                $from_name = 'Unknown Location';
            }

            $to_name = '';
            if ($to == '1') {
                $to_name = 'Surabaya';
            } elseif ($to == '2') {
                $to_name = 'Denpasar';
            } else {
                $to_name = 'Unknown Location';
            }

            // Prepare SQL query
            $sql = "SELECT pesawat.nama_pesawat, bookings_pesawat.detail
                        FROM pesawat
                        JOIN bookings_pesawat ON pesawat.id_pesawat = bookings_pesawat.id_pesawat
                        WHERE bookings_pesawat.from_id = ?
                        AND bookings_pesawat.to_id = ?
                        AND bookings_pesawat.departure_date = ?";

            if (!empty($returnDate)) {
                $sql .= " AND bookings_pesawat.return_date = ?";
            }

            // Prepare and bind parameters
            $stmt = $conn->prepare($sql);
            if (!empty($returnDate)) {
                $stmt->bind_param("ssss", $from, $to, $departureDate, $returnDate);
            } else {
                $stmt->bind_param("sss", $from, $to, $departureDate);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            ?>

            <div class="search-details">
                <p><strong>Dari:</strong> <?= htmlspecialchars($from_name) ?></p>
                <p><strong>Ke:</strong> <?= htmlspecialchars($to_name) ?></p>
                <p><strong>Pergi:</strong> <?= htmlspecialchars($departureDate) ?></p>
                <?php if (!empty($returnDate)) : ?>
                    <p><strong>Pulang:</strong> <?= htmlspecialchars($returnDate) ?></p>
                <?php endif; ?>
                <p><strong>Jumlah Kursi:</strong> <?= htmlspecialchars($passengers) ?></p>
            </div>

            <div class="bus-results">
                <?php if ($result->num_rows > 0) : ?>
                    <table>
                        <thead>
                            <tr>
                                <th align="center">Maskapai</th>
                                <th align="center">Informasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()) : ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['nama_pesawat']) ?></td>
                                    <td><a href="<?= htmlspecialchars($row['detail']) ?>" target="_blank">Lihat Selengkapnya</a></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <p>Tidak ditemukan hasil untuk pencarian Anda.</p>
                <?php endif; ?>
            </div>

            <?php
            // Close connection
            $stmt->close();
            $conn->close();
            ?>
        </div>
    </div>
</body>

</html>