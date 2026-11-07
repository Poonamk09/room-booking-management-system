<?php
session_start();
include "../config/database.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";

// Check Food ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid Food Item ID!");
}

$id = (int)$_GET['id'];

// Fetch Food Item
$stmt = mysqli_prepare($conn, "SELECT * FROM food_items WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$food = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$food) {
    die("Food Item Not Found!");
}

// Update Food Item
if (isset($_POST['update_food'])) {

    $item_name = trim($_POST['item_name']);
    $price = (float)$_POST['price'];

    if ($item_name == "" || $price <= 0) {

        $message = "Please enter valid food item details!";

    } else {

        $sql = "UPDATE food_items
                SET item_name = ?, price = ?
                WHERE id = ?";

        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param(
            $stmt,
            "sdi",
            $item_name,
            $price,
            $id
        );

        if (mysqli_stmt_execute($stmt)) {

            $message = "Food Item Updated Successfully!";

            $food['item_name'] = $item_name;
            $food['price'] = $price;

        } else {

            $message = "Unable to update food item!";

        }

        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html>
<head>

    <title>Edit Food Item - Admin Panel</title>

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
            max-width: 650px;
            margin: 45px auto;
            padding: 0 20px;
        }

        .form-box {
            background: white;
            padding: 35px;
            border-radius: 16px;
            box-shadow: 0 8px 25px rgba(80,60,150,0.12);
        }

        .form-box h2 {
            text-align: center;
            color: #5533a5;
            margin-bottom: 25px;
        }

        .message {
            background: #d4edda;
            color: #155724;
            padding: 13px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: bold;
        }

        .form-group {
            margin-bottom: 22px;
        }

        label {
            display: block;
            margin-bottom: 9px;
            font-weight: bold;
            color: #5533a5;
        }

        input {
            width: 100%;
            padding: 14px;
            border: 1px solid #ccc;
            border-radius: 8px;
            outline: none;
            font-size: 15px;
        }

        input:focus {
            border-color: #764ba2;
            box-shadow: 0 0 5px rgba(118,75,162,0.3);
        }

        .submit-btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        .submit-btn:hover {
            background: linear-gradient(135deg, #764ba2, #5533a5);
        }

        .back-btn {
            display: block;
            text-align: center;
            margin-top: 22px;
            color: #5533a5;
            text-decoration: none;
            font-weight: bold;
        }

        footer {
            background: #30205c;
            color: white;
            text-align: center;
            padding: 22px;
            margin-top: 60px;
        }

        @media(max-width: 600px) {
            header {
                justify-content: center;
                text-align: center;
            }

            .form-box {
                padding: 23px;
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

    <h1>✏ Edit Food Item</h1>

    <p>Update food item name and price.</p>

</section>


<div class="container">

    <div class="form-box">

        <h2>Food Item Information</h2>

        <?php if ($message != "") { ?>

            <div class="message">
                <?php echo htmlspecialchars($message); ?>
            </div>

        <?php } ?>


        <form method="POST">

            <div class="form-group">

                <label>Food Item Name</label>

                <input type="text"
                       name="item_name"
                       value="<?php echo htmlspecialchars($food['item_name']); ?>"
                       required>

            </div>


            <div class="form-group">

                <label>Food Item Price (₹)</label>

                <input type="number"
                       name="price"
                       min="0.01"
                       step="0.01"
                       value="<?php echo htmlspecialchars($food['price']); ?>"
                       required>

            </div>


            <button type="submit"
                    name="update_food"
                    class="submit-btn">

                💾 Update Food Item

            </button>

        </form>


        <a href="food_items.php" class="back-btn">
            ← Back to Manage Food Items
        </a>

    </div>

</div>


<footer>

    <p>© 2026 Room Booking System | Admin Panel</p>

</footer>

</body>
</html>