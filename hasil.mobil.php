<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="styles/hasil.mobil.css" />
    <title>Hasil Pencarian Mobil</title>
    <style>
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
        th, td {
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
                <a class="nav-item" href="destination.html">Destination</a>
                <a class="nav-item" href="about.html">About</a>
                <a class="nav-item" href="contact.html">Contact</a>
                <a class="nav-item" href="visa.php">Visa</a>
                <a class="nav-item" href="transport.php">Transport</a>
                <a class="nav-item active" href="tiket.php">Tiket</a>
            </div>
        </div>
    </div>

    <div class="results">
        <div class="main-content">
            <div class="header">
                <a class="back-button" href="sewa.mobil.php">Kembali</a>
                <h1>Hasil Pencarian Mobil</h1>
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
                $destination = $_GET['destination'];
                $check_in = $_GET['check-in-date'];
                $check_out = $_GET['check-out-date'];
                $rooms = $_GET['rooms'];

                // Mapping destination IDs to names
                $destination_name = '';
                if ($destination == '1') {
                    $destination_name = 'Surabaya';
                } elseif ($destination == '2') {
                    $destination_name = 'Denpasar';
                } else {
                    $destination_name = 'Unknown Destination';
                }

                // Prepare SQL query
                $sql = "SELECT car.vendor, car.name, bookings_mobil.detail 
                        FROM car
                        JOIN bookings_mobil ON car.id_car = bookings_mobil.id_car
                        WHERE bookings_mobil.id_destinations = ?
                        AND bookings_mobil.check_in_date = ?";

                if (!empty($check_out)) {
                    $sql .= " AND bookings_mobil.check_out_date = ?";
                } 

                if (!empty($rooms)) {
                    $sql .= " AND bookings_mobil.rooms = ?";
                }

                // Prepare and bind parameters
                $stmt = $conn->prepare($sql);

                if (!empty($check_out) && !empty($rooms)) {
                    $stmt->bind_param("issi", $destination, $check_in, $check_out, $rooms);
                } else {
                    $stmt->bind_param("is", $destination, $check_in);
                }
                $stmt->execute();
                $result = $stmt->get_result();
            ?>

            <div class="search-details">
                <p><strong>Tujuan:</strong> <?= htmlspecialchars($destination_name) ?></p>
                <p><strong>Check-In:</strong> <?= htmlspecialchars($check_in) ?></p>
                <p><strong>Check-Out:</strong> <?= htmlspecialchars($check_out) ?></p>
                <p><strong>Jumlah Mobil:</strong> <?= htmlspecialchars($rooms) ?></p>
            </div>

            <div class="car-results">
                <?php if ($result->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th align="center">Vendor</th>
                                <th align="center">Mobil</th>
                                <th align="center">Informasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['vendor']) ?></td>
                                    <td><?= htmlspecialchars($row['name']) ?></td>
                                    <td><a href="<?= htmlspecialchars($row['detail']) ?>" target="_blank">Lihat Selengkapnya</a></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
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
