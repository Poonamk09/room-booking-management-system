<?php
session_start();
include "../config/database.php";

// Check Admin Login
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$admin_name = $_SESSION['admin_name'] ?? "Admin";

// Total Users
$users_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role = 'user'");
$total_users = mysqli_fetch_assoc($users_result)['total'];

// Total Rooms
$rooms_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM rooms");
$total_rooms = mysqli_fetch_assoc($rooms_result)['total'];

// Total Bookings
$bookings_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM bookings");
$total_bookings = mysqli_fetch_assoc($bookings_result)['total'];

// Total Payments
$payments_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM payments");
$total_payments = mysqli_fetch_assoc($payments_result)['total'];

// Total Food Items
$food_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM food_items");
$total_food = mysqli_fetch_assoc($food_result)['total'];

?>

<!DOCTYPE html>
<html>
<head>

    <title>Admin Dashboard - Room Booking System</title>

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

        /* Welcome Section */

        .welcome-section {
            background: linear-gradient(135deg, #e0c3fc, #8ec5fc);
            padding: 40px 7%;
        }

        .welcome-section h1 {
            color: #38206b;
            font-size: 32px;
            margin-bottom: 10px;
        }

        .welcome-section p {
            color: #444;
            font-size: 16px;
        }

        /* Main Container */

        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .section-title {
            color: #5533a5;
            font-size: 25px;
            margin-bottom: 25px;
        }

        /* Statistics Cards */

        .stats-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 22px;
        }

        .stat-card {
            background: white;
            padding: 28px 20px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 8px 22px rgba(80,60,150,0.10);
            border-bottom: 5px solid #764ba2;
            transition: 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-7px);
            box-shadow: 0 12px 28px rgba(80,60,150,0.18);
        }

        .stat-icon {
            font-size: 40px;
            margin-bottom: 12px;
        }

        .stat-card h2 {
            color: #5533a5;
            font-size: 32px;
            margin-bottom: 8px;
        }

        .stat-card p {
            color: #777;
            font-weight: bold;
        }

        .users-card {
            border-color: #667eea;
        }

        .rooms-card {
            border-color: #28a745;
        }

        .bookings-card {
            border-color: #f5a623;
        }

        .payments-card {
            border-color: #17a2b8;
        }

        .food-card {
            border-color: #e83e8c;
        }

        /* Management Section */

        .management-section {
            margin-top: 45px;
        }

        .management-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 22px;
        }

        .management-card {
            background: white;
            padding: 28px 20px;
            text-align: center;
            border-radius: 15px;
            box-shadow: 0 8px 22px rgba(80,60,150,0.10);
            transition: 0.3s;
        }

        .management-card:hover {
            transform: translateY(-6px);
        }

        .management-icon {
            font-size: 42px;
            margin-bottom: 15px;
        }

        .management-card h3 {
            color: #5533a5;
            margin-bottom: 12px;
        }

        .management-card p {
            color: #777;
            line-height: 1.5;
            margin-bottom: 20px;
        }

        .manage-btn {
            display: inline-block;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            text-decoration: none;
            padding: 12px 22px;
            border-radius: 8px;
            font-weight: bold;
        }

        .manage-btn:hover {
            background: linear-gradient(135deg, #764ba2, #5533a5);
        }

        /* Add Room Button */

        .add-room-section {
            text-align: center;
            margin-top: 35px;
        }

        .add-room-btn {
            display: inline-block;
            background: #28a745;
            color: white;
            text-decoration: none;
            padding: 14px 28px;
            border-radius: 8px;
            font-weight: bold;
        }

        .add-room-btn:hover {
            background: #218838;
        }

        /* Logout */

        .logout-section {
            text-align: center;
            margin: 40px 0;
        }

        .logout-btn {
            display: inline-block;
            background: #dc3545;
            color: white;
            text-decoration: none;
            padding: 13px 35px;
            border-radius: 8px;
            font-weight: bold;
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

        @media(max-width: 850px) {

            .stats-container,
            .management-container {
                grid-template-columns: repeat(2, 1fr);
            }

        }

        @media(max-width: 550px) {

            header {
                justify-content: center;
                text-align: center;
            }

            .stats-container,
            .management-container {
                grid-template-columns: 1fr;
            }

            .welcome-section h1 {
                font-size: 26px;
            }

        }

    </style>

</head>

<body>

<header>

    <h2>🏨 Room Booking System</h2>

    <a href="logout.php">🚪 Logout</a>

</header>


<!-- Welcome Section -->

<section class="welcome-section">

    <h1>
        Welcome, <?php echo htmlspecialchars($admin_name); ?>! 👋
    </h1>

    <p>Admin Dashboard - Manage your Room Booking System.</p>

</section>


<div class="container">

    <!-- Statistics -->

    <h2 class="section-title">📊 Dashboard Overview</h2>

    <div class="stats-container">

        <div class="stat-card users-card">

            <div class="stat-icon">👥</div>

            <h2><?php echo $total_users; ?></h2>

            <p>Total Users</p>

        </div>


        <div class="stat-card rooms-card">

            <div class="stat-icon">🏨</div>

            <h2><?php echo $total_rooms; ?></h2>

            <p>Total Rooms</p>

        </div>


        <div class="stat-card bookings-card">

            <div class="stat-icon">📋</div>

            <h2><?php echo $total_bookings; ?></h2>

            <p>Total Bookings</p>

        </div>


        <div class="stat-card payments-card">

            <div class="stat-icon">💳</div>

            <h2><?php echo $total_payments; ?></h2>

            <p>Total Payments</p>

        </div>


        <div class="stat-card food-card">

            <div class="stat-icon">🍽️</div>

            <h2><?php echo $total_food; ?></h2>

            <p>Total Food Items</p>

        </div>

    </div>


    <!-- Management Section -->

    <div class="management-section">

        <h2 class="section-title">⚙️ Manage Your System</h2>

        <div class="management-container">

            <div class="management-card">

                <div class="management-icon">👥</div>

                <h3>Manage Users</h3>

                <p>View and manage all registered users.</p>

                <a href="users.php" class="manage-btn">
                    Manage Users →
                </a>

            </div>


            <div class="management-card">

                <div class="management-icon">🏨</div>

                <h3>Manage Rooms</h3>

                <p>Add, edit and delete room details.</p>

                <a href="rooms.php" class="manage-btn">
                    Manage Rooms →
                </a>

            </div>


            <div class="management-card">

                <div class="management-icon">📋</div>

                <h3>Manage Bookings</h3>

                <p>View bookings and update booking status.</p>

                <a href="bookings.php" class="manage-btn">
                    Manage Bookings →
                </a>

            </div>


            <div class="management-card">

                <div class="management-icon">💳</div>

                <h3>Manage Payments</h3>

                <p>View payment records and payment status.</p>

                <a href="payments.php" class="manage-btn">
                    Manage Payments →
                </a>

            </div>


            <div class="management-card">

                <div class="management-icon">🍽️</div>

                <h3>Manage Food Items</h3>

                <p>Add, edit and delete food item details.</p>

                <a href="food_items.php" class="manage-btn">
                    Manage Food →
                </a>

            </div>

        </div>

    </div>


    <!-- Add Room -->

    <div class="add-room-section">

        <a href="add_room.php" class="add-room-btn">
            ➕ Add New Room
        </a>

    </div>


    <!-- Logout -->

    <div class="logout-section">

        <a href="logout.php" class="logout-btn">
            🚪 Logout
        </a>

    </div>

</div>


<footer>

    <p>© 2026 Room Booking System | Admin Panel</p>

</footer>

</body>
</html>