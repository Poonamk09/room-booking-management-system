<?php
session_start();
include "config/database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$booking_id = isset($_GET['booking_id'])
    ? (int)$_GET['booking_id']
    : 0;

if ($booking_id <= 0) {
    header("Location: my_bookings.php");
    exit();
}

// Booking Details
$sql = "SELECT b.*, r.room_name, r.floor, r.ac_type,
               r.room_type, r.bed_type
        FROM bookings b
        JOIN rooms r ON b.room_id = r.id
        WHERE b.id = ? AND b.user_id = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $booking_id, $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$booking = mysqli_fetch_assoc($result);

if (!$booking) {
    header("Location: my_bookings.php");
    exit();
}

// Food Details
$food_sql = "SELECT fi.item_name, bf.quantity, bf.subtotal
             FROM booking_food bf
             JOIN food_items fi ON bf.food_id = fi.id
             WHERE bf.booking_id = ?";

$food_stmt = mysqli_prepare($conn, $food_sql);
mysqli_stmt_bind_param($food_stmt, "i", $booking_id);
mysqli_stmt_execute($food_stmt);

$food_result = mysqli_stmt_get_result($food_stmt);

$food_items = [];
$food_amount = 0;

while ($food = mysqli_fetch_assoc($food_result)) {
    $food_items[] = $food;
    $food_amount += (float)$food['subtotal'];
}

// Payment Details
$payment_sql = "SELECT * FROM payments WHERE booking_id = ?";

$payment_stmt = mysqli_prepare($conn, $payment_sql);
mysqli_stmt_bind_param($payment_stmt, "i", $booking_id);
mysqli_stmt_execute($payment_stmt);

$payment_result = mysqli_stmt_get_result($payment_stmt);
$payment = mysqli_fetch_assoc($payment_result);

$room_amount = (float)$booking['total_amount'];
$total_amount = $room_amount + $food_amount;

?>

<!DOCTYPE html>
<html>
<head>

    <title>Booking Confirmation - Room Booking System</title>

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

        .success-banner {
            background: linear-gradient(135deg, #667eea, #764ba2);
            text-align: center;
            color: white;
            padding: 45px 20px;
        }

        .success-icon {
            font-size: 65px;
            margin-bottom: 15px;
        }

        .success-banner h1 {
            font-size: 34px;
            margin-bottom: 12px;
        }

        .success-banner p {
            font-size: 17px;
        }

        .confirmation-container {
            max-width: 850px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .confirmation-card {
            background: white;
            padding: 35px;
            border-radius: 18px;
            box-shadow: 0 8px 25px rgba(80,60,150,0.12);
        }

        .confirmation-card h2 {
            color: #5533a5;
            margin-bottom: 22px;
        }

        .booking-id {
            background: #f0ebff;
            color: #5533a5;
            padding: 17px;
            text-align: center;
            border-radius: 10px;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 25px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            padding: 14px 0;
            border-bottom: 1px solid #eee;
        }

        .detail-row strong {
            color: #5533a5;
        }

        .food-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .food-table th {
            background: #764ba2;
            color: white;
            padding: 13px;
            text-align: left;
        }

        .food-table td {
            padding: 13px;
            border-bottom: 1px solid #eee;
        }

        .food-table tr:nth-child(even) {
            background: #f8f6ff;
        }

        .amount-box {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 23px;
            border-radius: 12px;
            margin-top: 25px;
        }

        .amount-row {
            display: flex;
            justify-content: space-between;
            padding: 9px 0;
        }

        .amount-total {
            border-top: 1px solid rgba(255,255,255,0.4);
            margin-top: 12px;
            padding-top: 17px;
            font-size: 23px;
            font-weight: bold;
        }

        .status {
            display: inline-block;
            padding: 7px 14px;
            border-radius: 20px;
            background: #fff0cf;
            color: #9a6500;
            font-weight: bold;
        }

        .button-group {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .btn {
            flex: 1;
            text-align: center;
            padding: 14px;
            border-radius: 9px;
            text-decoration: none;
            font-weight: bold;
        }

        .primary-btn {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .secondary-btn {
            background: #eee8ff;
            color: #5533a5;
        }

        .btn:hover {
            opacity: 0.85;
        }

        footer {
            background: #30205c;
            color: white;
            text-align: center;
            padding: 22px;
            margin-top: 40px;
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

            .confirmation-card {
                padding: 22px 16px;
            }

            .success-banner h1 {
                font-size: 27px;
            }

            .button-group {
                flex-direction: column;
            }

            .food-table {
                font-size: 13px;
            }

            .food-table th,
            .food-table td {
                padding: 9px 5px;
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

<section class="success-banner">

    <div class="success-icon">🎉</div>

    <h1>Thank You for Booking!</h1>

    <p>Your booking details are displayed below.</p>

</section>

<div class="confirmation-container">

    <div class="confirmation-card">

        <div class="booking-id">
            Booking ID: #<?php echo (int)$booking['id']; ?>
        </div>

        <h2>🏨 Room Details</h2>

        <div class="detail-row">
            <strong>Room Name</strong>
            <span><?php echo htmlspecialchars($booking['room_name']); ?></span>
        </div>

        <div class="detail-row">
            <strong>Floor</strong>
            <span><?php echo htmlspecialchars($booking['floor']); ?></span>
        </div>

        <div class="detail-row">
            <strong>Room Type</strong>
            <span><?php echo htmlspecialchars($booking['room_type']); ?></span>
        </div>

        <div class="detail-row">
            <strong>AC Type</strong>
            <span><?php echo htmlspecialchars($booking['ac_type']); ?></span>
        </div>

        <div class="detail-row">
            <strong>Bed Type</strong>
            <span><?php echo htmlspecialchars($booking['bed_type']); ?></span>
        </div>

        <div class="detail-row">
            <strong>Check-in Date</strong>
            <span><?php echo htmlspecialchars($booking['check_in']); ?></span>
        </div>

        <div class="detail-row">
            <strong>Check-out Date</strong>
            <span><?php echo htmlspecialchars($booking['check_out']); ?></span>
        </div>

        <div class="detail-row">
            <strong>Members</strong>
            <span><?php echo (int)$booking['members']; ?></span>
        </div>

        <div class="detail-row">
            <strong>Booking Status</strong>
            <span class="status">
                <?php echo htmlspecialchars($booking['booking_status']); ?>
            </span>
        </div>

        <h2 style="margin-top:30px;">🍽️ Food Details</h2>

        <?php if (count($food_items) > 0) { ?>

            <table class="food-table">

                <tr>
                    <th>Food Item</th>
                    <th>Quantity</th>
                    <th>Amount</th>
                </tr>

                <?php foreach ($food_items as $food) { ?>

                    <tr>
                        <td><?php echo htmlspecialchars($food['item_name']); ?></td>
                        <td><?php echo (int)$food['quantity']; ?></td>
                        <td>₹<?php echo number_format((float)$food['subtotal'], 2); ?></td>
                    </tr>

                <?php } ?>

            </table>

        <?php } else { ?>

            <p>No Food Items Selected.</p>

        <?php } ?>

        <h2 style="margin-top:30px;">💳 Payment Details</h2>

        <div class="detail-row">
            <strong>Payment Method</strong>
            <span>
                <?php echo $payment
                    ? htmlspecialchars($payment['payment_method'])
                    : 'Not Available'; ?>
            </span>
        </div>

        <div class="detail-row">
            <strong>Payment Status</strong>
            <span class="status">
                <?php echo $payment
                    ? htmlspecialchars($payment['payment_status'])
                    : 'Pending'; ?>
            </span>
        </div>

        <div class="amount-box">

            <div class="amount-row">
                <span>Room Charges</span>
                <span>₹<?php echo number_format($room_amount, 2); ?></span>
            </div>

            <div class="amount-row">
                <span>Food Charges</span>
                <span>₹<?php echo number_format($food_amount, 2); ?></span>
            </div>

            <div class="amount-row amount-total">
                <span>Total Amount</span>
                <span>₹<?php echo number_format($total_amount, 2); ?></span>
            </div>

        </div>

        <div class="button-group">

            <a href="user_dashboard.php" class="btn primary-btn">
                Go to Dashboard
            </a>

            <a href="my_bookings.php" class="btn secondary-btn">
                My Bookings
            </a>

        </div>

    </div>

</div>

<footer>

    <p>© 2026 Room Booking System | All Rights Reserved</p>

</footer>

</body>
</html>