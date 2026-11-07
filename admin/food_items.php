<?php
session_start();
include "../config/database.php";

// Check Admin Login
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch Food Items
$sql = "SELECT * FROM food_items ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>

    <title>Manage Food Items - Admin Panel</title>
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
            max-width: 1100px;
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

        .food-count {
            background: #eee9ff;
            color: #5533a5;
            padding: 12px 18px;
            border-radius: 8px;
            display: inline-block;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .add-btn {
            display: inline-block;
            background: #28a745;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .add-btn:hover {
            background: #218838;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 650px;
        }

        th {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 15px;
            text-align: center;
        }

        td {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #eee;
        }

        tr:hover {
            background: #f8f6ff;
        }

        .price {
            color: #218838;
            font-weight: bold;
        }

        .edit-btn {
            display: inline-block;
            background: #ffc107;
            color: #333;
            padding: 8px 13px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            margin: 3px;
        }

        .edit-btn:hover {
            background: #e0a800;
        }

        .delete-btn {
            background: #dc3545;
            color: white;
            padding: 8px 13px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            margin: 3px;
        }

        .delete-btn:hover {
            background: #b52a37;
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

    <h1>🍽️ Manage Food Items</h1>

    <p>Add, view, edit and manage food items and their prices.</p>

</section>


<div class="container">

    <div class="table-box">

        <h2>Food Item Details</h2>

        <div class="food-count">
            Total Food Items: <?php echo mysqli_num_rows($result); ?>
        </div>

        <br>

        <a href="add_food.php" class="add-btn">
            ➕ Add New Food Item
        </a>


        <?php if (mysqli_num_rows($result) > 0) { ?>

            <table>

                <tr>
                    <th>Food ID</th>
                    <th>Food Item Name</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>


                <?php while ($row = mysqli_fetch_assoc($result)) { ?>

                    <tr>

                        <td>
                            #<?php echo (int)$row['id']; ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['item_name']); ?>
                        </td>

                        <td class="price">
                            ₹<?php echo number_format((float)$row['price'], 2); ?>
                        </td>

                        <td>

                            <a href="edit_food.php?id=<?php echo (int)$row['id']; ?>"
                               class="edit-btn">
                                ✏ Edit
                            </a>

                            <form method="POST"
                                  action="delete_food.php"
                                  style="display:inline;"
                                  onsubmit="return confirm('Are you sure you want to delete this food item?');">

                                <input type="hidden"
                                       name="id"
                                       value="<?php echo (int)$row['id']; ?>">

                                <button type="submit" class="delete-btn">
                                    🗑 Delete
                                </button>

                            </form>

                        </td>

                    </tr>

                <?php } ?>

            </table>

        <?php } else { ?>

            <div class="empty-box">
                <h3>📭 No Food Items Found!</h3>
                <p>Please add a new food item.</p>
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