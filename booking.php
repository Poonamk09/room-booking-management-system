<?php
session_start();
include "config/database.php";

// Check User Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$error = "";

// Get Room ID
$room_id = isset($_GET['room_id']) ? (int)$_GET['room_id'] : 0;

if ($room_id <= 0) {
    header("Location: rooms.php");
    exit();
}

// Fetch Room Details
$sql = "SELECT * FROM rooms WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $room_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$room = mysqli_fetch_assoc($result);

if (!$room) {
    header("Location: rooms.php");
    exit();
}

// Booking Process
if (isset($_POST['book_room'])) {

    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $members = (int)$_POST['members'];

    $today = date('Y-m-d');

    if ($check_in < $today) {

        $error = "Check-in date cannot be in the past!";

    } elseif ($check_out <= $check_in) {

        $error = "Check-out date must be after check-in date!";

    } elseif ($members < 1 || $members > $room['capacity']) {

        $error = "Invalid number of members! Maximum capacity is " . $room['capacity'];

    } else {

        $date1 = new DateTime($check_in);
        $date2 = new DateTime($check_out);

        $days = $date1->diff($date2)->days;

        $total_amount = $days * $room['price'];

        // Check Room Availability
        $check_sql = "SELECT id FROM bookings
                      WHERE room_id = ?
                      AND booking_status != 'Cancelled'
                      AND check_in < ?
                      AND check_out > ?";

        $check_stmt = mysqli_prepare($conn, $check_sql);

        mysqli_stmt_bind_param(
            $check_stmt,
            "iss",
            $room_id,
            $check_out,
            $check_in
        );

        mysqli_stmt_execute($check_stmt);

        $check_result = mysqli_stmt_get_result($check_stmt);

        if (mysqli_num_rows($check_result) > 0) {

            $error = "Sorry! This room is already booked for selected dates.";

        } else {

            // Insert Booking
            $insert_sql = "INSERT INTO bookings
                           (user_id, room_id, check_in, check_out,
                            members, total_amount, booking_status)
                           VALUES (?, ?, ?, ?, ?, ?, 'Pending')";

            $insert_stmt = mysqli_prepare($conn, $insert_sql);

            mysqli_stmt_bind_param(
                $insert_stmt,
                "iissid",
                $user_id,
                $room_id,
                $check_in,
                $check_out,
                $members,
                $total_amount
            );

            if (mysqli_stmt_execute($insert_stmt)) {

                $booking_id = mysqli_insert_id($conn);

                header("Location: food.php?booking_id=" . $booking_id);
                exit();

            } else {

                $error = "Booking Failed! Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>

    <title>Book Room - Room Booking System</title>

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
            margin-left: 20px;
            font-weight: bold;
        }

        nav a:hover {
            color: #ffe082;
        }

        .page-heading {
            text-align: center;
            padding: 40px 20px;
            background: linear-gradient(135deg, #e0c3fc, #8ec5fc);
        }

        .page-heading h1 {
            color: #38206b;
            font-size: 35px;
            margin-bottom: 10px;
        }

        .page-heading p {
            color: #444;
            font-size: 16px;
        }

        .booking-container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        .room-info,
        .booking-form {
            background: white;
            padding: 30px;
            border-radius: 18px;
            box-shadow: 0 8px 25px rgba(80,60,150,0.12);
        }

        .room-info h2,
        .booking-form h2 {
            color: #5533a5;
            margin-bottom: 25px;
            font-size: 23px;
        }

        .room-icon {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            font-size: 55px;
            text-align: center;
            padding: 35px;
            border-radius: 14px;
            margin-bottom: 22px;
        }

        .room-detail {
            display: flex;
            justify-content: space-between;
            padding: 13px 0;
            border-bottom: 1px solid #eee;
            gap: 10px;
        }

        .room-detail strong {
            color: #5533a5;
        }

        .price-box {
            background: #f0ebff;
            padding: 18px;
            border-radius: 12px;
            text-align: center;
            margin-top: 22px;
        }

        .price-box h3 {
            color: #5533a5;
            font-size: 28px;
        }

        .price-box p {
            color: #777;
            margin-top: 5px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 9px;
            color: #444;
        }

        .form-group input {
            width: 100%;
            padding: 13px;
            border: 1px solid #ddd;
            border-radius: 9px;
            font-size: 15px;
            outline: none;
        }

        .form-group input:focus {
            border-color: #764ba2;
            box-shadow: 0 0 5px rgba(118,75,162,0.3);
        }

        .booking-btn {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 9px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            font-size: 17px;
            font-weight: bold;
            cursor: pointer;
        }

        .booking-btn:hover {
            background: linear-gradient(135deg, #764ba2, #5533a5);
        }

        .error-message {
            background: #ffe1e1;
            color: #c62828;
            padding: 13px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #764ba2;
            text-decoration: none;
            font-weight: bold;
        }

        footer {
            background: #30205c;
            color: white;
            text-align: center;
            padding: 22px;
            margin-top: 40px;
        }

        @media(max-width: 750px) {
            .booking-container {
                grid-template-columns: 1fr;
            }

            header {
                justify-content: center;
                text-align: center;
            }

            nav a {
                display: inline-block;
                margin: 8px;
            }

            .page-heading h1 {
                font-size: 28px;
            }
        }

    </style>

</head>

<body>

<header>

    <h2>🏨 Room Booking System</h2>

    <nav>
        <a href="index.php">Home</a>
        <a href="rooms.php">Rooms</a>
        <a href="user_dashboard.php">Dashboard</a>
        <a href="my_bookings.php">My Bookings</a>
    </nav>

</header>

<section class="page-heading">

    <h1>✨ Book Your Room</h1>

    <p>Complete your booking details and enjoy your stay!</p>

</section>

<div class="booking-container">

    <!-- Room Details -->

    <div class="room-info">

        <h2>🏨 Selected Room Details</h2>

        <div class="room-icon">🛏️</div>

        <div class="room-detail">
            <strong>Room Name</strong>
            <span><?php echo htmlspecialchars($room['room_name']); ?></span>
        </div>

        <div class="room-detail">
            <strong>Floor</strong>
            <span><?php echo htmlspecialchars($room['floor']); ?></span>
        </div>

        <div class="room-detail">
            <strong>Room Type</strong>
            <span><?php echo htmlspecialchars($room['room_type']); ?></span>
        </div>

        <div class="room-detail">
            <strong>AC Type</strong>
            <span><?php echo htmlspecialchars($room['ac_type']); ?></span>
        </div>

        <div class="room-detail">
            <strong>Bed Type</strong>
            <span><?php echo htmlspecialchars($room['bed_type']); ?></span>
        </div>

        <div class="room-detail">
            <strong>Maximum Capacity</strong>
            <span><?php echo (int)$room['capacity']; ?> Persons</span>
        </div>

        <div class="price-box">

            <h3>₹<?php echo number_format((float)$room['price'], 2); ?></h3>

            <p>Room Price Per Day</p>

        </div>

    </div>


    <!-- Booking Form -->

    <div class="booking-form">

        <h2>📅 Booking Information</h2>

        <?php if ($error != "") { ?>

            <div class="error-message">
                <?php echo htmlspecialchars($error); ?>
            </div>

        <?php } ?>

        <form method="POST" action="">

            <div class="form-group">

                <label>Check-in Date</label>

                <input
                    type="date"
                    name="check_in"
                    min="<?php echo date('Y-m-d'); ?>"
                    value="<?php echo isset($_POST['check_in']) ? htmlspecialchars($_POST['check_in']) : ''; ?>"
                    required
                >

            </div>

            <div class="form-group">

                <label>Check-out Date</label>

                <input
                    type="date"
                    name="check_out"
                    min="<?php echo date('Y-m-d'); ?>"
                    value="<?php echo isset($_POST['check_out']) ? htmlspecialchars($_POST['check_out']) : ''; ?>"
                    required
                >

            </div>

            <div class="form-group">

                <label>Number of Members</label>

                <input
                    type="number"
                    name="members"
                    min="1"
                    max="<?php echo (int)$room['capacity']; ?>"
                    value="<?php echo isset($_POST['members']) ? (int)$_POST['members'] : 1; ?>"
                    required
                >

            </div>

            <button type="submit" name="book_room" class="booking-btn">
                Confirm Booking →
            </button>

        </form>

        <a href="rooms.php" class="back-link">
            ← Back to Rooms
        </a>

    </div>

</div>

<footer>

    <p>© 2026 Room Booking System | All Rights Reserved</p>

</footer>

</body>
</html>