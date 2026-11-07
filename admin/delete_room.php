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

// Delete Room
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {

    $id = (int)$_POST['id'];

    if ($id <= 0) {
        $message = "Invalid Room ID!";
        $message_type = "error";
    } else {

        // Check whether room has bookings
        $check_sql = "SELECT COUNT(*) AS total
                      FROM bookings
                      WHERE room_id = ?";

        $check_stmt = mysqli_prepare($conn, $check_sql);
        mysqli_stmt_bind_param($check_stmt, "i", $id);
        mysqli_stmt_execute($check_stmt);

        $check_result = mysqli_stmt_get_result($check_stmt);
        $check_data = mysqli_fetch_assoc($check_result);

        mysqli_stmt_close($check_stmt);

        if ($check_data['total'] > 0) {

            $message = "This room cannot be deleted because bookings are linked to it.";
            $message_type = "error";

        } else {

            // Delete room
            $delete_sql = "DELETE FROM rooms WHERE id = ?";

            $delete_stmt = mysqli_prepare($conn, $delete_sql);
            mysqli_stmt_bind_param($delete_stmt, "i", $id);

            if (mysqli_stmt_execute($delete_stmt)) {

                if (mysqli_stmt_affected_rows($delete_stmt) > 0) {
                    $message = "Room deleted successfully!";
                    $message_type = "success";
                } else {
                    $message = "Room not found!";
                    $message_type = "error";
                }

            } else {

                $message = "Unable to delete room!";
                $message_type = "error";

            }

            mysqli_stmt_close($delete_stmt);
        }
    }

} else {

    $message = "Invalid request!";
    $message_type = "error";

}
?>

<!DOCTYPE html>
<html>
<head>

    <title>Delete Room - Admin Panel</title>

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

        .container {
            max-width: 650px;
            margin: 100px auto;
            padding: 20px;
        }

        .message-box {
            background: white;
            padding: 45px 30px;
            text-align: center;
            border-radius: 18px;
            box-shadow: 0 8px 25px rgba(80,60,150,0.12);
        }

        .icon {
            font-size: 65px;
            margin-bottom: 20px;
        }

        h1 {
            color: #5533a5;
            margin-bottom: 15px;
        }

        .message {
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            font-weight: bold;
            line-height: 1.6;
        }

        .success {
            background: #d4edda;
            color: #155724;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
        }

        .btn {
            display: inline-block;
            padding: 13px 22px;
            margin: 8px;
            border-radius: 8px;
            text-decoration: none;
            color: white;
            font-weight: bold;
            background: linear-gradient(135deg, #667eea, #764ba2);
        }

        .btn:hover {
            opacity: 0.9;
        }

        footer {
            background: #30205c;
            color: white;
            text-align: center;
            padding: 22px;
            margin-top: 100px;
        }

        @media(max-width: 600px) {
            header {
                justify-content: center;
                text-align: center;
            }
        }

    </style>

</head>

<body>

<header>

    <h2>🏨 Room Booking System</h2>

    <a href="dashboard.php">⬅ Admin Dashboard</a>

</header>


<div class="container">

    <div class="message-box">

        <?php if ($message_type == "success") { ?>

            <div class="icon">✅</div>

            <h1>Room Deleted!</h1>

        <?php } else { ?>

            <div class="icon">⚠️</div>

            <h1>Unable to Delete Room</h1>

        <?php } ?>


        <div class="message <?php echo $message_type; ?>">

            <?php echo htmlspecialchars($message); ?>

        </div>


        <a href="rooms.php" class="btn">
            🏨 Manage Rooms
        </a>

        <a href="dashboard.php" class="btn">
            🏠 Dashboard
        </a>

    </div>

</div>


<footer>

    <p>© 2026 Room Booking System | Admin Panel</p>

</footer>

</body>
</html>