<?php
session_start();
include "../config/database.php";

// Check Admin Login
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch Users
$sql = "SELECT id, name, email, phone, role, created_at
        FROM users
        WHERE role = 'user'
        ORDER BY id DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>

    <title>Manage Users - Admin Panel</title>

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

        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

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

        .user-count {
            background: #eee9ff;
            color: #5533a5;
            padding: 12px 18px;
            border-radius: 8px;
            display: inline-block;
            margin-bottom: 20px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 750px;
        }

        th {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 15px 12px;
            text-align: center;
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

        .user-badge {
            background: #e0e7ff;
            color: #4338ca;
            padding: 7px 13px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 12px;
        }

        .empty-box {
            text-align: center;
            padding: 40px;
            color: #777;
        }

        .back-btn {
            display: inline-block;
            margin-top: 25px;
            padding: 12px 22px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
        }

        .back-btn:hover {
            background: #5533a5;
        }

        footer {
            background: #30205c;
            color: white;
            text-align: center;
            padding: 22px;
            margin-top: 50px;
        }

        @media(max-width: 600px) {

            header {
                justify-content: center;
                text-align: center;
            }

            .page-heading h1 {
                font-size: 26px;
            }

        }

    </style>

</head>

<body>

<header>

    <h2>🏨 Room Booking System</h2>

    <a href="dashboard.php">⬅ Admin Dashboard</a>

</header>


<section class="page-heading">

    <h1>👥 Manage Users</h1>

    <p>View all registered users and their details.</p>

</section>


<div class="container">

    <div class="table-box">

        <h2>Registered Users</h2>

        <div class="user-count">
            Total Users: <?php echo mysqli_num_rows($result); ?>
        </div>

        <?php if (mysqli_num_rows($result) > 0) { ?>

            <table>

                <tr>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Email Address</th>
                    <th>Phone Number</th>
                    <th>Registration Date</th>
                    <th>Role</th>
                </tr>

                <?php while ($row = mysqli_fetch_assoc($result)) { ?>

                    <tr>

                        <td>
                            #<?php echo (int)$row['id']; ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['name']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['email']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['phone']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['created_at']); ?>
                        </td>

                        <td>
                            <span class="user-badge">
                                <?php echo htmlspecialchars($row['role']); ?>
                            </span>
                        </td>

                    </tr>

                <?php } ?>

            </table>

        <?php } else { ?>

            <div class="empty-box">
                <h3>📭 No Registered Users Found!</h3>
                <p>Currently, there are no registered users.</p>
            </div>

        <?php } ?>

    </div>

    <a href="dashboard.php" class="back-btn">
        ← Back to Dashboard
    </a>

</div>


<footer>

    <p>© 2026 Room Booking System | Admin Panel</p>

</footer>

</body>
</html>