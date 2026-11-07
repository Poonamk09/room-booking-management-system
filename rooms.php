<?php
session_start();
include "config/database.php";

$sql = "SELECT * FROM rooms ORDER BY id ASC";
$result = mysqli_query($conn, $sql);

$is_logged_in = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html>
<head>

    <title>Available Rooms - Room Booking System</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background: #f4f3ff;
            color: #333;
        }

        /* Header */

        header {
            background: linear-gradient(135deg, #667eea, #764ba2);
            padding: 22px 8%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        header h2 {
            color: white;
            font-size: 25px;
        }

        nav a {
            color: white;
            text-decoration: none;
            margin-left: 22px;
            font-weight: bold;
            font-size: 15px;
        }

        nav a:hover {
            color: #ffe082;
        }

        /* Heading */

        .rooms-heading {
            text-align: center;
            padding: 55px 20px 35px;
            background: linear-gradient(135deg, #e0c3fc, #8ec5fc);
        }

        .rooms-heading h1 {
            font-size: 38px;
            color: #38206b;
            margin-bottom: 12px;
        }

        .rooms-heading p {
            color: #444;
            font-size: 17px;
        }

        /* Room Container */

        .rooms-container {
            max-width: 1250px;
            margin: 45px auto;
            padding: 0 25px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 28px;
        }

        /* Room Card */

        .room-card {
            background: white;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(80, 60, 150, 0.12);
            transition: 0.3s;
            border: 1px solid #eee;
        }

        .room-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(80, 60, 150, 0.22);
        }

        .room-top {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 25px 20px;
            text-align: center;
        }

        .room-icon {
            font-size: 42px;
            margin-bottom: 10px;
        }

        .room-top h2 {
            font-size: 21px;
        }

        .room-card-content {
            padding: 25px;
        }

        .room-details {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }

        .room-details strong {
            color: #5533a5;
        }

        .room-price {
            text-align: center;
            margin: 22px 0;
            color: #5533a5;
            font-size: 29px;
            font-weight: bold;
        }

        .room-price span {
            font-size: 14px;
            color: #777;
            font-weight: normal;
        }

        /* Book Button */

        .room-btn {
            display: block;
            text-align: center;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 14px;
            border-radius: 9px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s;
        }

        .room-btn:hover {
            background: linear-gradient(135deg, #764ba2, #5533a5);
            transform: scale(1.02);
        }

        .no-rooms {
            grid-column: 1 / -1;
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 15px;
            color: #764ba2;
            font-size: 20px;
        }

        /* Footer */

        footer {
            background: #30205c;
            color: white;
            text-align: center;
            padding: 22px;
            margin-top: 50px;
        }

        /* Responsive */

        @media(max-width: 950px) {
            .rooms-container {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media(max-width: 600px) {

            header {
                justify-content: center;
                text-align: center;
            }

            nav a {
                display: inline-block;
                margin: 8px;
            }

            .rooms-container {
                grid-template-columns: 1fr;
            }

            .rooms-heading h1 {
                font-size: 29px;
            }
        }

    </style>

</head>

<body>

<!-- Header -->

<header>

    <h2>🏨 Room Booking System</h2>

    <nav>

        <a href="index.php">Home</a>

        <a href="rooms.php">Rooms</a>

        <?php if ($is_logged_in) { ?>

            <a href="user_dashboard.php">Dashboard</a>

            <a href="my_bookings.php">My Bookings</a>

        <?php } else { ?>

            <a href="login.php">Login</a>

            <a href="register.php">Register</a>

        <?php } ?>

    </nav>

</header>


<!-- Page Heading -->

<section class="rooms-heading">

    <h1>✨ Available Rooms</h1>

    <p>Choose your perfect room and enjoy your comfortable stay.</p>

</section>


<!-- Rooms -->

<div class="rooms-container">

    <?php if (mysqli_num_rows($result) > 0) { ?>

        <?php while ($room = mysqli_fetch_assoc($result)) { ?>

            <div class="room-card">

                <div class="room-top">

                    <div class="room-icon">🛏️</div>

                    <h2>
                        <?php echo htmlspecialchars($room['room_name']); ?>
                    </h2>

                </div>

                <div class="room-card-content">

                    <div class="room-details">
                        <strong>Floor</strong>
                        <span>
                            <?php echo htmlspecialchars($room['floor']); ?>
                        </span>
                    </div>

                    <div class="room-details">
                        <strong>Room Type</strong>
                        <span>
                            <?php echo htmlspecialchars($room['room_type']); ?>
                        </span>
                    </div>

                    <div class="room-details">
                        <strong>AC Type</strong>
                        <span>
                            <?php echo htmlspecialchars($room['ac_type']); ?>
                        </span>
                    </div>

                    <div class="room-details">
                        <strong>Bed Type</strong>
                        <span>
                            <?php echo htmlspecialchars($room['bed_type']); ?>
                        </span>
                    </div>

                    <div class="room-details">
                        <strong>Capacity</strong>
                        <span>
                            <?php echo (int)$room['capacity']; ?> Persons
                        </span>
                    </div>

                    <div class="room-price">

                        ₹<?php echo number_format((float)$room['price'], 2); ?>

                        <span>/ Day</span>

                    </div>

                    <a
                        href="booking.php?room_id=<?php echo (int)$room['id']; ?>"
                        class="room-btn">
                        Book Now →
                    </a>

                </div>

            </div>

        <?php } ?>

    <?php } else { ?>

        <p class="no-rooms">No Rooms Available Currently.</p>

    <?php } ?>

</div>


<!-- Footer -->

<footer>

    <p>© 2026 Room Booking System | All Rights Reserved</p>

</footer>

</body>
</html>