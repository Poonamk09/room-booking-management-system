<?php
session_start();
include "config/database.php";

// Check User Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$error = "";

// Get Booking ID
$booking_id = isset($_GET['booking_id'])
    ? (int)$_GET['booking_id']
    : (isset($_POST['booking_id']) ? (int)$_POST['booking_id'] : 0);

if ($booking_id <= 0) {
    header("Location: my_bookings.php");
    exit();
}

// Check Booking Ownership
$sql = "SELECT * FROM bookings
        WHERE id = ? AND user_id = ?
        AND booking_status != 'Cancelled'";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $booking_id, $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$booking = mysqli_fetch_assoc($result);

if (!$booking) {
    header("Location: my_bookings.php");
    exit();
}

// Fetch Food Items
$food_sql = "SELECT * FROM food_items ORDER BY id ASC";
$food_result = mysqli_query($conn, $food_sql);

$food_items = [];

while ($item = mysqli_fetch_assoc($food_result)) {
    $food_items[] = $item;
}

// Save Food Selection
if (isset($_POST['continue'])) {

    $quantities = isset($_POST['quantity']) ? $_POST['quantity'] : [];

    // Remove previous selection for this booking
    $delete_sql = "DELETE FROM booking_food WHERE booking_id = ?";
    $delete_stmt = mysqli_prepare($conn, $delete_sql);
    mysqli_stmt_bind_param($delete_stmt, "i", $booking_id);
    mysqli_stmt_execute($delete_stmt);

    // Insert Selected Food Items
    $insert_sql = "INSERT INTO booking_food
                   (booking_id, food_id, quantity, subtotal)
                   VALUES (?, ?, ?, ?)";

    $insert_stmt = mysqli_prepare($conn, $insert_sql);

    foreach ($food_items as $item) {

        $food_id = (int)$item['id'];
        $price = (float)$item['price'];

        $quantity = isset($quantities[$food_id])
            ? (int)$quantities[$food_id]
            : 0;

        if ($quantity < 0 || $quantity > 100) {
            $error = "Please enter a valid quantity (0 to 100).";
            break;
        }

        if ($quantity > 0) {

            $subtotal = $price * $quantity;

            mysqli_stmt_bind_param(
                $insert_stmt,
                "iiid",
                $booking_id,
                $food_id,
                $quantity,
                $subtotal
            );

            if (!mysqli_stmt_execute($insert_stmt)) {
                $error = "Unable to save food selection. Please try again.";
                break;
            }
        }
    }

    if ($error == "") {
        header("Location: payment.php?booking_id=" . $booking_id);
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>

    <title>Food Selection - Room Booking System</title>

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
            font-size: 16px;
        }

        .food-container {
            max-width: 1100px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .food-card {
            background: white;
            padding: 28px;
            border-radius: 18px;
            box-shadow: 0 8px 25px rgba(80,60,150,0.12);
        }

        .food-card h2 {
            color: #5533a5;
            margin-bottom: 22px;
        }

        .food-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .food-item {
            border: 1px solid #e5def8;
            border-radius: 14px;
            padding: 22px;
            text-align: center;
            background: #fcfbff;
            transition: 0.3s;
        }

        .food-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(80,60,150,0.12);
        }

        .food-icon {
            font-size: 42px;
            margin-bottom: 12px;
        }

        .food-item h3 {
            color: #5533a5;
            margin-bottom: 10px;
        }

        .food-price {
            color: #16834a;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 18px;
        }

        .food-item label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }

        .food-item input {
            width: 100%;
            padding: 11px;
            border: 1px solid #ccc;
            border-radius: 8px;
            text-align: center;
            font-size: 16px;
        }

        .continue-btn {
            display: block;
            width: 100%;
            margin-top: 28px;
            padding: 15px;
            border: none;
            border-radius: 9px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            font-size: 17px;
            font-weight: bold;
            cursor: pointer;
        }

        .continue-btn:hover {
            background: linear-gradient(135deg, #764ba2, #5533a5);
        }

        .skip-link {
            display: block;
            text-align: center;
            margin-top: 18px;
            color: #764ba2;
            text-decoration: none;
            font-weight: bold;
        }

        .error-message {
            background: #ffe1e1;
            color: #c62828;
            padding: 13px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }

        .empty-message {
            text-align: center;
            color: #777;
            padding: 25px;
        }

        footer {
            background: #30205c;
            color: white;
            text-align: center;
            padding: 22px;
            margin-top: 40px;
        }

        @media(max-width: 850px) {
            .food-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media(max-width: 550px) {
            header {
                justify-content: center;
                text-align: center;
            }

            nav a {
                display: inline-block;
                margin: 8px;
            }

            .food-grid {
                grid-template-columns: 1fr;
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

    <h1>🍽️ Food Selection</h1>

    <p>Select your favourite food items for your booking.</p>

</section>

<div class="food-container">

    <div class="food-card">

        <h2>✨ Available Food Items</h2>

        <?php if ($error != "") { ?>

            <div class="error-message">
                <?php echo htmlspecialchars($error); ?>
            </div>

        <?php } ?>

        <?php if (count($food_items) > 0) { ?>

            <form method="POST" action="">

                <input
                    type="hidden"
                    name="booking_id"
                    value="<?php echo $booking_id; ?>"
                >

                <div class="food-grid">

                    <?php foreach ($food_items as $item) { ?>

                        <?php
                        $icon = "🍽️";

                        $item_name = strtolower($item['item_name']);

                        if (strpos($item_name, "water") !== false) {
                            $icon = "💧";
                        } elseif (strpos($item_name, "colgate") !== false) {
                            $icon = "🪥";
                        } elseif (strpos($item_name, "shampoo") !== false) {
                            $icon = "🧴";
                        } elseif (strpos($item_name, "tea") !== false) {
                            $icon = "☕";
                        } elseif (strpos($item_name, "coffee") !== false) {
                            $icon = "☕";
                        } elseif (strpos($item_name, "nasta") !== false) {
                            $icon = "🥪";
                        } elseif (strpos($item_name, "thali") !== false) {
                            $icon = "🍛";
                        }
                        ?>

                        <div class="food-item">

                            <div class="food-icon">
                                <?php echo $icon; ?>
                            </div>

                            <h3>
                                <?php echo htmlspecialchars($item['item_name']); ?>
                            </h3>

                            <div class="food-price">
                                ₹<?php echo number_format((float)$item['price'], 2); ?>
                            </div>

                            <label>Quantity</label>

                            <input
                                type="number"
                                name="quantity[<?php echo (int)$item['id']; ?>]"
                                min="0"
                                max="100"
                                value="0"
                            >

                        </div>

                    <?php } ?>

                </div>

                <button type="submit" name="continue" class="continue-btn">
                    Continue to Payment →
                </button>

            </form>

        <?php } else { ?>

            <p class="empty-message">
                No Food Items Available Currently.
            </p>

        <?php } ?>

        <a
            href="payment.php?booking_id=<?php echo $booking_id; ?>"
            class="skip-link">
            Skip Food Selection →
        </a>

    </div>

</div>

<footer>

    <p>© 2026 Room Booking System | All Rights Reserved</p>

</footer>

</body>
</html>