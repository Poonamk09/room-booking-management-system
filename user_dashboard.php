<?php
session_start();
include "config/database.php";

// Check User Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];
$user_name = $_SESSION['user_name'] ?? "User";

// Total Bookings
$total_sql = "SELECT COUNT(*) AS total
              FROM bookings
              WHERE user_id = $user_id";
$total_result = mysqli_query($conn, $total_sql);
$total_bookings = mysqli_fetch_assoc($total_result)['total'];

// Pending Bookings
$pending_sql = "SELECT COUNT(*) AS total
                FROM bookings
                WHERE user_id = $user_id
                AND booking_status = 'Pending'";
$pending_result = mysqli_query($conn, $pending_sql);
$pending_bookings = mysqli_fetch_assoc($pending_result)['total'];

// Confirmed Bookings
$confirmed_sql = "SELECT COUNT(*) AS total
                  FROM bookings
                  WHERE user_id = $user_id
                  AND booking_status = 'Confirmed'";
$confirmed_result = mysqli_query($conn, $confirmed_sql);
$confirmed_bookings = mysqli_fetch_assoc($confirmed_result)['total'];

// Cancelled Bookings
$cancelled_sql = "SELECT COUNT(*) AS total
                  FROM bookings
                  WHERE user_id = $user_id
                  AND booking_status = 'Cancelled'";
$cancelled_result = mysqli_query($conn, $cancelled_sql);
$cancelled_bookings = mysqli_fetch_assoc($cancelled_result)['total'];

// Logout
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();

    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>

    <title>User Dashboard - Room Booking System</title>

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
            margin-left: 20px;
            font-weight: bold;
        }

        nav a:hover {
            color: #ffe082;
        }

        /* Welcome Section */

        .welcome-section {
            background: linear-gradient(135deg, #e0c3fc, #8ec5fc);
            padding: 45px 8%;
        }

        .welcome-section h1 {
            color: #38206b;
            font-size: 34px;
            margin-bottom: 12px;
        }

        .welcome-section p {
            color: #444;
            font-size: 17px;
        }

        /* Main Container */

        .dashboard-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 25px;
        }

        .section-title {
            color: #5533a5;
            font-size: 25px;
            margin-bottom: 25px;
        }

        /* Statistics Cards */

        .stats-container {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 22px;
        }

        .stat-card {
            background: white;
            padding: 27px 20px;
            border-radius: 16px;
            box-shadow: 0 8px 22px rgba(80,60,150,0.10);
            text-align: center;
            transition: 0.3s;
            border-bottom: 5px solid #764ba2;
        }

        .stat-card:hover {
            transform: translateY(-7px);
            box-shadow: 0 12px 28px rgba(80,60,150,0.18);
        }

        .stat-icon {
            font-size: 38px;
            margin-bottom: 12px;
        }

        .stat-card h2 {
            font-size: 32px;
            color: #5533a5;
            margin-bottom: 8px;
        }

        .stat-card p {
            color: #777;
            font-weight: bold;
        }

        .total-card {
            border-color: #667eea;
        }

        .pending-card {
            border-color: #f5a623;
        }

        .confirmed-card {
            border-color: #28a745;
        }

        .cancelled-card {
            border-color: #dc3545;
        }

        /* Quick Actions */

        .actions-section {
            margin-top: 45px;
        }

        .actions-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 22px;
        }

        .action-card {
            background: white;
            padding: 30px 20px;
            border-radius: 16px;
            text-align: center;
            box-shadow: 0 8px 22px rgba(80,60,150,0.10);
            transition: 0.3s;
        }

        .action-card:hover {
            transform: translateY(-6px);
        }

        .action-icon {
            font-size: 42px;
            margin-bottom: 15px;
        }

        .action-card h3 {
            color: #5533a5;
            margin-bottom: 12px;
        }

        .action-card p {
            color: #777;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .action-btn {
            display: inline-block;
            padding: 12px 22px;
            border-radius: 8px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        .action-btn:hover {
            background: linear-gradient(135deg, #764ba2, #5533a5);
        }

        /* Logout */

        .logout-section {
            text-align: center;
            margin: 40px 0;
        }

        .logout-btn {
            padding: 13px 35px;
            border: none;
            border-radius: 8px;
            background: #dc3545;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        .logout-btn:hover {
            background: #b52a37;
        }

        /* Footer */

        footer {
            background: #30205c;
            color: white;
            text-align: center;
            padding: 22px;
            margin-top: 40px;
        }

        /* Responsive */

        @media(max-width: 900px) {

            .stats-container {
                grid-template-columns: repeat(2, 1fr);
            }

            .actions-container {
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

            .stats-container,
            .actions-container {
                grid-template-columns: 1fr;
            }

            .welcome-section h1 {
                font-size: 27px;
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
        <a href="my_bookings.php">My Bookings</a>
    </nav>

</header>


<!-- Welcome Section -->

<section class="welcome-section">

    <h1>
        Welcome, <?php echo htmlspecialchars($user_name); ?>! 👋
    </h1>

    <p>Manage your bookings and enjoy a comfortable stay with us.</p>

</section>


<div class="dashboard-container">

    <h2 class="section-title">📊 Booking Overview</h2>

    <!-- Statistics -->

    <div class="stats-container">

        <div class="stat-card total-card">

            <div class="stat-icon">📋</div>

            <h2><?php echo $total_bookings; ?></h2>

            <p>Total Bookings</p>

        </div>


        <div class="stat-card pending-card">

            <div class="stat-icon">⏳</div>

            <h2><?php echo $pending_bookings; ?></h2>

            <p>Pending Bookings</p>

        </div>


        <div class="stat-card confirmed-card">

            <div class="stat-icon">✅</div>

            <h2><?php echo $confirmed_bookings; ?></h2>

            <p>Confirmed Bookings</p>

        </div>


        <div class="stat-card cancelled-card">

            <div class="stat-icon">❌</div>

            <h2><?php echo $cancelled_bookings; ?></h2>

            <p>Cancelled Bookings</p>

        </div>

    </div>


    <!-- Quick Actions -->

    <div class="actions-section">

        <h2 class="section-title">⚡ Quick Actions</h2>

        <div class="actions-container">

            <div class="action-card">

                <div class="action-icon">🏨</div>

                <h3>Browse Rooms</h3>

                <p>Explore available rooms and select your favourite room.</p>

                <a href="rooms.php" class="action-btn">
                    View Rooms →
                </a>

            </div>


            <div class="action-card">

                <div class="action-icon">📖</div>

                <h3>My Bookings</h3>

                <p>View your booking history, payment and booking status.</p>

                <a href="my_bookings.php" class="action-btn">
                    View Bookings →
                </a>

            </div>


            <div class="action-card">

                <div class="action-icon">💳</div>

                <h3>Payment Details</h3>

                <p>Check your booking payment information and charges.</p>

                <a href="my_bookings.php" class="action-btn">
                    View Details →
                </a>

            </div>

        </div>

    </div>


    <!-- Logout -->

    <div class="logout-section">

        <form method="POST" action="">

            <button type="submit" name="logout" class="logout-btn">
                🚪 Logout
            </button>

        </form>

    </div>

</div>


<footer>

    <p>© 2026 Room Booking System | All Rights Reserved</p>

</footer>

</body>
</html>