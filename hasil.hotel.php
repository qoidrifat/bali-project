<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="styles/hasil.hotel.css" />
    <title>Hasil Pencarian Hotel</title>
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
                <a class="nav-item" href="transport.php">Transport</a>
                <a class="nav-item active" href="tiket.php">Tiket</a>
            </div>
        </div>
    </div>

    <div class="results">
        <div class="main-content">
            <div class="header">
                <a class="back-button" href="booking.hotel.php">Kembali</a>
                <h1>Hasil Pencarian Hotel</h1>
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
            $sql = "SELECT hotel.name, bookings_hotel.detail 
                        FROM hotel
                        JOIN bookings_hotel ON hotel.id_hotel = bookings_hotel.id_hotel
                        WHERE bookings_hotel.id_destinations = ?
                        AND bookings_hotel.check_in_date = ?";

            if (!empty($check_out)) {
                $sql .= " AND bookings_hotel.check_out_date = ?";
            }

            if (!empty($rooms)) {
                $sql .= " AND bookings_hotel.rooms = ?";
            }

            // Prepare and bind parameters
            $stmt = $conn->prepare($sql);

            if (!empty($check_out) && !empty($rooms)) {
                $stmt->bind_param("issi", $destination, $check_in, $check_out, $rooms);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            ?>

            <div class="search-details">
                <p><strong>Tujuan:</strong> <?= htmlspecialchars($destination_name) ?></p>
                <p><strong>Check-In:</strong> <?= htmlspecialchars($check_in) ?></p>
                <p><strong>Check-Out:</strong> <?= htmlspecialchars($check_out) ?></p>
                <p><strong>Jumlah Kamar:</strong> <?= htmlspecialchars($rooms) ?></p>
            </div>

            <div class="hotel-results">
                <?php if ($result->num_rows > 0) : ?>
                    <table>
                        <thead>
                            <tr>
                                <th align="center">Hotel</th>
                                <th align="center">Informasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()) : ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['name']) ?></td>
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