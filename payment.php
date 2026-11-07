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
    : (isset($_POST['booking_id']) ? (int)$_POST['booking_id'] : 0);

if ($booking_id <= 0) {
    header("Location: my_bookings.php");
    exit();
}

// Fetch Booking Details
$sql = "SELECT b.*, r.room_name
        FROM bookings b
        JOIN rooms r ON b.room_id = r.id
        WHERE b.id = ? AND b.user_id = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $booking_id, $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$booking = mysqli_fetch_assoc($result);

if (!$booking || $booking['booking_status'] == 'Cancelled') {
    header("Location: my_bookings.php");
    exit();
}

// Calculate Food Charges
$food_sql = "SELECT COALESCE(SUM(subtotal), 0) AS food_total
             FROM booking_food
             WHERE booking_id = ?";

$food_stmt = mysqli_prepare($conn, $food_sql);
mysqli_stmt_bind_param($food_stmt, "i", $booking_id);
mysqli_stmt_execute($food_stmt);

$food_result = mysqli_stmt_get_result($food_stmt);
$food_data = mysqli_fetch_assoc($food_result);

$room_amount = (float)$booking['total_amount'];
$food_amount = (float)$food_data['food_total'];
$total_amount = $room_amount + $food_amount;

$error = "";

// Payment Process
if (isset($_POST['pay_now'])) {

    $payment_method = $_POST['payment_method'];

    if (!in_array($payment_method, ['Cash on Arrival', 'UPI Demo'], true)) {

        $error = "Please select a valid payment method!";

    } else {

        // Check Existing Payment
        $check_sql = "SELECT id FROM payments WHERE booking_id = ?";
        $check_stmt = mysqli_prepare($conn, $check_sql);

        mysqli_stmt_bind_param($check_stmt, "i", $booking_id);
        mysqli_stmt_execute($check_stmt);

        $check_result = mysqli_stmt_get_result($check_stmt);

        if (mysqli_num_rows($check_result) > 0) {

            $update_sql = "UPDATE payments
                           SET amount = ?, payment_method = ?,
                               payment_status = 'Pending'
                           WHERE booking_id = ?";

            $update_stmt = mysqli_prepare($conn, $update_sql);

            mysqli_stmt_bind_param(
                $update_stmt,
                "dsi",
                $total_amount,
                $payment_method,
                $booking_id
            );

            mysqli_stmt_execute($update_stmt);

        } else {

            $insert_sql = "INSERT INTO payments
                           (booking_id, amount, payment_method, payment_status)
                           VALUES (?, ?, ?, 'Pending')";

            $insert_stmt = mysqli_prepare($conn, $insert_sql);

            mysqli_stmt_bind_param(
                $insert_stmt,
                "ids",
                $booking_id,
                $total_amount,
                $payment_method
            );

            mysqli_stmt_execute($insert_stmt);
        }

        header("Location: confirmation.php?booking_id=" . $booking_id);
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>

    <title>Payment - Room Booking System</title>

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
        }

        .payment-container {
            max-width: 750px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .payment-card {
            background: white;
            padding: 35px;
            border-radius: 18px;
            box-shadow: 0 8px 25px rgba(80,60,150,0.12);
        }

        .payment-card h2 {
            color: #5533a5;
            margin-bottom: 25px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            padding: 16px 0;
            border-bottom: 1px solid #eee;
        }

        .summary-row strong {
            color: #5533a5;
        }

        .total-box {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 22px;
            border-radius: 12px;
            text-align: center;
            margin: 25px 0;
        }

        .total-box p {
            margin-bottom: 8px;
        }

        .total-box h2 {
            color: white;
            font-size: 32px;
            margin: 0;
        }

        .payment-method {
            margin-top: 25px;
        }

        .payment-method h3 {
            color: #5533a5;
            margin-bottom: 15px;
        }

        .method-option {
            display: block;
            background: #f8f6ff;
            border: 1px solid #e2dafa;
            border-radius: 10px;
            padding: 16px;
            margin-bottom: 13px;
            cursor: pointer;
        }

        .method-option:hover {
            background: #eee8ff;
        }

        .method-option input {
            margin-right: 10px;
            accent-color: #764ba2;
        }

        .pay-btn {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 9px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            font-size: 17px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 20px;
        }

        .pay-btn:hover {
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

        .note {
            background: #fff4d6;
            color: #795500;
            padding: 14px;
            border-radius: 9px;
            margin-top: 20px;
            font-size: 14px;
            line-height: 1.5;
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

            .payment-card {
                padding: 24px 18px;
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

    <h1>💳 Payment Details</h1>

    <p>Review your booking amount and select payment method.</p>

</section>

<div class="payment-container">

    <div class="payment-card">

        <h2>🧾 Booking Summary</h2>

        <div class="summary-row">
            <strong>Booking ID</strong>
            <span>#<?php echo (int)$booking['id']; ?></span>
        </div>

        <div class="summary-row">
            <strong>Room Name</strong>
            <span><?php echo htmlspecialchars($booking['room_name']); ?></span>
        </div>

        <div class="summary-row">
            <strong>Check-in</strong>
            <span><?php echo htmlspecialchars($booking['check_in']); ?></span>
        </div>

        <div class="summary-row">
            <strong>Check-out</strong>
            <span><?php echo htmlspecialchars($booking['check_out']); ?></span>
        </div>

        <div class="summary-row">
            <strong>Room Charges</strong>
            <span>₹<?php echo number_format($room_amount, 2); ?></span>
        </div>

        <div class="summary-row">
            <strong>Food Charges</strong>
            <span>₹<?php echo number_format($food_amount, 2); ?></span>
        </div>

        <div class="total-box">

            <p>Total Payable Amount</p>

            <h2>₹<?php echo number_format($total_amount, 2); ?></h2>

        </div>

        <?php if ($error != "") { ?>

            <div class="error-message">
                <?php echo htmlspecialchars($error); ?>
            </div>

        <?php } ?>

        <form method="POST" action="">

            <input
                type="hidden"
                name="booking_id"
                value="<?php echo $booking_id; ?>"
            >

            <div class="payment-method">

                <h3>Select Payment Method</h3>

                <label class="method-option">

                    <input
                        type="radio"
                        name="payment_method"
                        value="Cash on Arrival"
                        required
                    >

                    💵 Cash on Arrival

                </label>

                <label class="method-option">

                    <input
                        type="radio"
                        name="payment_method"
                        value="UPI Demo"
                        required
                    >

                    📱 UPI Payment (Demo)

                </label>

            </div>

            <button type="submit" name="pay_now" class="pay-btn">
                Confirm Payment →
            </button>

        </form>

        <div class="note">

            <strong>Note:</strong>
            UPI Payment is currently for demonstration only.
            No actual money will be transferred.

        </div>

    </div>

</div>

<footer>

    <p>© 2026 Room Booking System | All Rights Reserved</p>

</footer>

</body>
</html>