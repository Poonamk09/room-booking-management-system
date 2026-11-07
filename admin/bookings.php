<?php
session_start();
include "../config/database.php";

// Check Admin Login
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";
$message_type = "";

// Update Booking Status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {

    $booking_id = (int)$_POST['booking_id'];
    $status = $_POST['booking_status'];

    $allowed_status = ['Pending', 'Confirmed', 'Cancelled'];

    if ($booking_id <= 0) {

        $message = "Invalid Booking ID!";
        $message_type = "error";

    } elseif (!in_array($status, $allowed_status, true)) {

        $message = "Invalid Booking Status!";
        $message_type = "error";

    } else {

        // Check Booking Exists
        $check_sql = "SELECT id FROM bookings WHERE id = ?";
        $check_stmt = mysqli_prepare($conn, $check_sql);

        mysqli_stmt_bind_param($check_stmt, "i", $booking_id);
        mysqli_stmt_execute($check_stmt);

        $check_result = mysqli_stmt_get_result($check_stmt);

        if (mysqli_num_rows($check_result) == 0) {

            $message = "Booking Not Found!";
            $message_type = "error";

        } else {

            // Update Status
            $sql = "UPDATE bookings SET booking_status = ? WHERE id = ?";

            $stmt = mysqli_prepare($conn, $sql);

            mysqli_stmt_bind_param($stmt, "si", $status, $booking_id);

            if (mysqli_stmt_execute($stmt)) {

                $message = "Booking #" . $booking_id . " status updated successfully!";
                $message_type = "success";

            } else {

                $message = "Unable to update booking status!";
                $message_type = "error";
            }

            mysqli_stmt_close($stmt);
        }

        mysqli_stmt_close($check_stmt);
    }
}

// Fetch Bookings
$sql = "SELECT
            b.id,
            b.check_in,
            b.check_out,
            b.members,
            b.total_amount,
            b.booking_status,
            u.name AS user_name,
            u.email,
            r.room_name
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        JOIN rooms r ON b.room_id = r.id
        ORDER BY b.id DESC";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Error fetching bookings: " . mysqli_error($conn));
}

$total_bookings = mysqli_num_rows($result);
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <title>Manage Bookings - Admin Panel</title>

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

        header a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        header a:hover {
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
        }

        /* Main Container */

        .container {
            max-width: 1350px;
            margin: 40px auto;
            padding: 0 20px;
        }

        /* Message */

        .message {
            padding: 16px;
            border-radius: 10px;
            margin-bottom: 22px;
            font-weight: bold;
            text-align: center;
        }

        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #b7dfc1;
        }

        .message.error {
            background: #ffe1e1;
            color: #c62828;
            border: 1px solid #ffbaba;
        }

        /* Table Box */

        .table-box {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 8px 22px rgba(80,60,150,0.10);
            overflow-x: auto;
        }

        .table-box h2 {
            color: #5533a5;
            margin-bottom: 20px;
        }

        .booking-count {
            background: #eee9ff;
            color: #5533a5;
            padding: 12px 18px;
            border-radius: 8px;
            display: inline-block;
            margin-bottom: 20px;
            font-weight: bold;
        }

        /* Table */

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1200px;
        }

        th {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 15px 10px;
            text-align: center;
            font-size: 14px;
            white-space: nowrap;
        }

        td {
            padding: 14px 10px;
            text-align: center;
            border-bottom: 1px solid #eee;
            font-size: 13px;
        }

        tr:hover {
            background: #f8f6ff;
        }

        .amount {
            color: #218838;
            font-weight: bold;
            white-space: nowrap;
        }

        /* Status Badge */

        .status {
            display: inline-block;
            padding: 7px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .pending {
            background: #fff3cd;
            color: #856404;
        }

        .confirmed {
            background: #d4edda;
            color: #155724;
        }

        .cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        /* Update Form */

        .update-form {
            min-width: 130px;
        }

        .status-select {
            width: 125px;
            padding: 9px;
            border: 1px solid #ccc;
            border-radius: 7px;
            outline: none;
            background: white;
            font-size: 13px;
            cursor: pointer;
        }

        .status-select:focus {
            border-color: #764ba2;
            box-shadow: 0 0 5px rgba(118,75,162,0.3);
        }

        .update-btn {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 9px 18px;
            border-radius: 7px;
            cursor: pointer;
            font-weight: bold;
            margin-top: 8px;
            transition: 0.3s;
        }

        .update-btn:hover {
            background: linear-gradient(135deg, #764ba2, #5533a5);
            transform: translateY(-2px);
            box-shadow: 0 5px 12px rgba(80,60,150,0.2);
        }

        /* Empty Box */

        .empty-box {
            text-align: center;
            padding: 40px;
            color: #777;
        }

        .empty-box h3 {
            color: #5533a5;
            margin-bottom: 10px;
        }

        /* Back Button */

        .back-btn {
            display: inline-block;
            margin-top: 25px;
            padding: 12px 22px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            transition: 0.3s;
        }

        .back-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 12px rgba(80,60,150,0.2);
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

        @media(max-width: 600px) {

            header {
                justify-content: center;
                text-align: center;
            }

            .page-heading h1 {
                font-size: 26px;
            }

            .table-box {
                padding: 15px;
            }
        }

    </style>

</head>

<body>

<!-- Header -->

<header>

    <h2>🏨 Room Booking System</h2>

    <a href="dashboard.php">⬅ Admin Dashboard</a>

</header>


<!-- Page Heading -->

<section class="page-heading">

    <h1>📋 Manage Bookings</h1>

    <p>View customer bookings and manage booking status.</p>

</section>


<!-- Main Container -->

<div class="container">

    <!-- Success / Error Message -->

    <?php if ($message != "") { ?>

        <div class="message <?php echo $message_type; ?>">

            <?php echo htmlspecialchars($message); ?>

        </div>

    <?php } ?>


    <!-- Booking Table -->

    <div class="table-box">

        <h2>All Booking Details</h2>

        <div class="booking-count">

            📊 Total Bookings: <?php echo $total_bookings; ?>

        </div>


        <?php if ($total_bookings > 0) { ?>

            <table>

                <thead>

                    <tr>
                        <th>Booking ID</th>
                        <th>User Name</th>
                        <th>Email</th>
                        <th>Room Name</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Members</th>
                        <th>Room Amount</th>
                        <th>Status</th>
                        <th>Update Status</th>
                    </tr>

                </thead>

                <tbody>

                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>

                        <tr>

                            <td>
                                #<?php echo (int)$row['id']; ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($row['user_name']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($row['email']); ?>
                            </td>

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

                            <td class="amount">
                                ₹<?php echo number_format((float)$row['total_amount'], 2); ?>
                            </td>

                            <!-- Current Status -->

                            <td>

                                <?php
                                $status_class = strtolower($row['booking_status']);
                                ?>

                                <span class="status <?php echo htmlspecialchars($status_class); ?>">

                                    <?php echo htmlspecialchars($row['booking_status']); ?>

                                </span>

                            </td>

                            <!-- Update Status Form -->

                            <td>

                                <form method="POST"
                                      action="bookings.php"
                                      class="update-form">

                                    <input type="hidden"
                                           name="booking_id"
                                           value="<?php echo (int)$row['id']; ?>">

                                    <input type="hidden"
                                           name="update_status"
                                           value="1">

                                    <select name="booking_status"
                                            class="status-select"
                                            required>

                                        <option value="Pending"
                                            <?php if ($row['booking_status'] == 'Pending') echo 'selected'; ?>>
                                            Pending
                                        </option>

                                        <option value="Confirmed"
                                            <?php if ($row['booking_status'] == 'Confirmed') echo 'selected'; ?>>
                                            Confirmed
                                        </option>

                                        <option value="Cancelled"
                                            <?php if ($row['booking_status'] == 'Cancelled') echo 'selected'; ?>>
                                            Cancelled
                                        </option>

                                    </select>

                                    <br>

                                    <button type="submit" class="update-btn">
                                        Update
                                    </button>

                                </form>

                            </td>

                        </tr>

                    <?php } ?>

                </tbody>

            </table>

        <?php } else { ?>

            <div class="empty-box">

                <h3>📭 No Bookings Found!</h3>

                <p>Currently, there are no bookings.</p>

            </div>

        <?php } ?>

    </div>


    <!-- Back Button -->

    <a href="dashboard.php" class="back-btn">
        ← Back to Dashboard
    </a>

</div>


<!-- Footer -->

<footer>

    <p>© 2026 Room Booking System | Admin Panel</p>

</footer>

</body>
</html>