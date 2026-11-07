<?php
session_start();
include "../config/database.php";

// Check Admin Login
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch Payment Details
$sql = "SELECT
            p.id AS payment_id,
            p.booking_id,
            p.amount,
            p.payment_method,
            p.payment_status,
            p.transaction_id,
            p.payment_date,
            u.name AS user_name,
            u.email
        FROM payments p
        JOIN bookings b ON p.booking_id = b.id
        JOIN users u ON b.user_id = u.id
        ORDER BY p.id DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>

    <title>Manage Payments - Admin Panel</title>

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
            max-width: 1250px;
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

        .payment-count {
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
            min-width: 1100px;
        }

        th {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 15px 10px;
            text-align: center;
            font-size: 14px;
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
        }

        .status {
            display: inline-block;
            padding: 7px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .Pending {
            background: #fff3cd;
            color: #856404;
        }

        .Paid {
            background: #d4edda;
            color: #155724;
        }

        .Failed {
            background: #f8d7da;
            color: #721c24;
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

    <h1>💳 Manage Payments</h1>

    <p>View and monitor all customer payment records.</p>

</section>


<div class="container">

    <div class="table-box">

        <h2>Payment Details</h2>

        <div class="payment-count">
            Total Payments: <?php echo mysqli_num_rows($result); ?>
        </div>


        <?php if (mysqli_num_rows($result) > 0) { ?>

            <table>

                <tr>
                    <th>Payment ID</th>
                    <th>Booking ID</th>
                    <th>Customer Name</th>
                    <th>Email</th>
                    <th>Amount</th>
                    <th>Payment Method</th>
                    <th>Payment Status</th>
                    <th>Transaction ID</th>
                    <th>Payment Date</th>
                </tr>


                <?php while ($row = mysqli_fetch_assoc($result)) { ?>

                    <?php
                    $status_class = preg_replace(
                        '/[^a-zA-Z0-9_-]/',
                        '',
                        $row['payment_status']
                    );
                    ?>

                    <tr>

                        <td>
                            #<?php echo (int)$row['payment_id']; ?>
                        </td>

                        <td>
                            #<?php echo (int)$row['booking_id']; ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['user_name']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['email']); ?>
                        </td>

                        <td class="amount">
                            ₹<?php echo number_format((float)$row['amount'], 2); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['payment_method']); ?>
                        </td>

                        <td>
                            <span class="status <?php echo htmlspecialchars($status_class); ?>">
                                <?php echo htmlspecialchars($row['payment_status']); ?>
                            </span>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['transaction_id'] ?? 'N/A'); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['payment_date'] ?? 'N/A'); ?>
                        </td>

                    </tr>

                <?php } ?>

            </table>

        <?php } else { ?>

            <div class="empty-box">
                <h3>📭 No Payment Records Found!</h3>
                <p>Currently, there are no payment records.</p>
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
