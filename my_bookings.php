<?php
session_start();
include "config/database.php";

// Check User Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];
$message = "";

// Cancel Booking
if (isset($_POST['cancel_booking'])) {

    $booking_id = (int)$_POST['booking_id'];

    $cancel_sql = "UPDATE bookings
                   SET booking_status = 'Cancelled'
                   WHERE id = ?
                   AND user_id = ?
                   AND booking_status IN ('Pending', 'Confirmed')";

    $stmt = mysqli_prepare($conn, $cancel_sql);
    mysqli_stmt_bind_param($stmt, "ii", $booking_id, $user_id);

    if (mysqli_stmt_execute($stmt)) {
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            $message = "Booking cancelled successfully!";
        } else {
            $message = "Booking could not be cancelled.";
        }
    } else {
        $message = "Something went wrong. Please try again.";
    }

    mysqli_stmt_close($stmt);
}

// Fetch Bookings
$sql = "SELECT 
            b.id,
            b.check_in,
            b.check_out,
            b.members,
            b.total_amount AS room_amount,
            b.booking_status,
            r.room_name,

            COALESCE(
                (SELECT SUM(bf.subtotal)
                 FROM booking_food bf
                 WHERE bf.booking_id = b.id), 0
            ) AS food_amount

        FROM bookings b

        JOIN rooms r ON b.room_id = r.id

        WHERE b.user_id = ?

        ORDER BY b.id DESC";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

?>

<!DOCTYPE html>
<html>
<head>

    <title>My Bookings - Room Booking System</title>

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
            padding: 22px 7%;
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

        /* Page Heading */

        .page-heading {
            background: linear-gradient(135deg, #e0c3fc, #8ec5fc);
            padding: 40px 7%;
        }

        .page-heading h1 {
            color: #38206b;
            font-size: 32px;
            margin-bottom: 10px;
        }

        .page-heading p {
            color: #444;
            font-size: 16px;
        }

        /* Container */

        .container {
            max-width: 1250px;
            margin: 40px auto;
            padding: 0 20px;
        }

        /* Message */

        .message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: bold;
        }

        /* Table */

        .table-box {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(80,60,150,0.10);
            overflow-x: auto;
        }

        .table-box h2 {
            color: #5533a5;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1050px;
        }

        th {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 15px 12px;
            text-align: center;
            font-size: 14px;
        }

        td {
            padding: 15px 12px;
            text-align: center;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }

        tr:hover {
            background: #f8f6ff;
        }

        /* Status */

        .status {
            display: inline-block;
            padding: 7px 12px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 12px;
        }

        .Pending {
            background: #fff3cd;
            color: #856404;
        }

        .Confirmed {
            background: #d4edda;
            color: #155724;
        }

        .Cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        /* Cancel Button */

        .cancel-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 9px 13px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }

        .cancel-btn:hover {
            background: #b52a37;
        }

        .cancelled-text {
            color: #999;
            font-weight: bold;
        }

        /* Empty Message */

        .empty-box {
            background: white;
            padding: 50px 20px;
            text-align: center;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(80,60,150,0.10);
        }

        .empty-box h2 {
            color: #5533a5;
            margin-bottom: 12px;
        }

        .empty-box p {
            color: #777;
            margin-bottom: 20px;
        }

        .book-btn {
            display: inline-block;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 12px 22px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
        }

        /* Footer */

        footer {
            background: #30205c;
            color: white;
            text-align: center;
            padding: 22px;
            margin-top: 50px;
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
    </nav>

</header>


<section class="page-heading">

    <h1>📖 My Bookings</h1>

    <p>View and manage all your room booking details.</p>

</section>


<div class="container">

    <?php if ($message != "") { ?>

        <div class="message">
            <?php echo htmlspecialchars($message); ?>
        </div>

    <?php } ?>


    <?php if (mysqli_num_rows($result) > 0) { ?>

        <div class="table-box">

            <h2>Booking History</h2>

            <table>

                <tr>
                    <th>Booking ID</th>
                    <th>Room Name</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Members</th>
                    <th>Room Charges</th>
                    <th>Food Charges</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>


                <?php while ($row = mysqli_fetch_assoc($result)) {

                    $room_amount = (float)$row['room_amount'];
                    $food_amount = (float)$row['food_amount'];
                    $total_amount = $room_amount + $food_amount;

                    $status = $row['booking_status'];

                ?>

                    <tr>

                        <td>#<?php echo $row['id']; ?></td>

                        <td>
                            <?php echo htmlspecialchars($row['room_name']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['check_in']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['check_out']); ?>
                        </td>

                        <td>
                            <?php echo (int)$row['members']; ?>
                        </td>

                        <td>
                            ₹<?php echo number_format($room_amount, 2); ?>
                        </td>

                        <td>
                            ₹<?php echo number_format($food_amount, 2); ?>
                        </td>

                        <td>
                            <strong>
                                ₹<?php echo number_format($total_amount, 2); ?>
                            </strong>
                        </td>

                        <td>

                            <span class="status <?php echo htmlspecialchars($status); ?>">
                                <?php echo htmlspecialchars($status); ?>
                            </span>

                        </td>

                        <td>

                            <?php if ($status == "Pending" || $status == "Confirmed") { ?>

                                <form method="POST" onsubmit="return confirm('Are you sure you want to cancel this booking?');">

                                    <input type="hidden"
                                           name="booking_id"
                                           value="<?php echo (int)$row['id']; ?>">

                                    <button type="submit"
                                            name="cancel_booking"
                                            class="cancel-btn">

                                        Cancel

                                    </button>

                                </form>

                            <?php } else { ?>

                                <span class="cancelled-text">
                                    No Action
                                </span>

                            <?php } ?>

                        </td>

                    </tr>

                <?php } ?>

            </table>

        </div>

    <?php } else { ?>

        <div class="empty-box">

            <h2>📭 No Bookings Found!</h2>

            <p>You have not booked any room yet.</p>

            <a href="rooms.php" class="book-btn">
                Book a Room →
            </a>

        </div>

    <?php } ?>

</div>


<footer>

    <p>© 2026 Room Booking System | All Rights Reserved</p>

</footer>

</body>
</html>

<?php
mysqli_stmt_close($stmt);
?>