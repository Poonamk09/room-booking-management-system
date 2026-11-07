<?php
session_start();
include "../config/database.php";

// Check Admin Login
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";

// Add New Room
if (isset($_POST['add_room'])) {

    $room_name = trim($_POST['room_name']);
    $floor = trim($_POST['floor']);
    $room_type = trim($_POST['room_type']);
    $ac_type = trim($_POST['ac_type']);
    $bed_type = trim($_POST['bed_type']);
    $capacity = (int)$_POST['capacity'];
    $price = (float)$_POST['price'];

    if (
        $room_name == "" || $floor == "" ||
        $room_type == "" || $ac_type == "" ||
        $bed_type == "" || $capacity <= 0 || $price <= 0
    ) {
        $message = "Please fill all fields with valid values!";
    } else {

        $sql = "INSERT INTO rooms
                (room_name, floor, room_type, ac_type, bed_type, capacity, price)
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param(
            $stmt,
            "sssssid",
            $room_name,
            $floor,
            $room_type,
            $ac_type,
            $bed_type,
            $capacity,
            $price
        );

        if (mysqli_stmt_execute($stmt)) {
            $message = "New room added successfully!";
        } else {
            $message = "Unable to add room. Please try again.";
        }

        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html>
<head>

    <title>Add New Room - Admin Panel</title>

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
            max-width: 750px;
            margin: 40px auto;
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
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #5533a5;
        }

        input, select {
            width: 100%;
            padding: 13px;
            border: 1px solid #ccc;
            border-radius: 8px;
            outline: none;
            font-size: 15px;
        }

        input:focus, select:focus {
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
            margin-top: 10px;
        }

        .submit-btn:hover {
            background: linear-gradient(135deg, #764ba2, #5533a5);
        }

        .back-btn {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #5533a5;
            text-decoration: none;
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

            .form-box {
                padding: 22px;
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

    <h1>➕ Add New Room</h1>

    <p>Add new room details to your booking system.</p>

</section>


<div class="container">

    <div class="form-box">

        <h2>Room Information</h2>

        <?php if ($message != "") { ?>

            <div class="message">
                <?php echo htmlspecialchars($message); ?>
            </div>

        <?php } ?>


        <form method="POST">

            <div class="form-group">

                <label>Room Name</label>

                <input type="text"
                       name="room_name"
                       placeholder="Enter room name"
                       required>

            </div>


            <div class="form-group">

                <label>Floor</label>

                <select name="floor" required>

                    <option value="">Select Floor</option>
                    <option value="Ground Floor">Ground Floor</option>
                    <option value="First Floor">First Floor</option>
                    <option value="Second Floor">Second Floor</option>

                </select>

            </div>


            <div class="form-group">

                <label>Room Type</label>

                <select name="room_type" required>

                    <option value="">Select Room Type</option>
                    <option value="Attached">Attached</option>
                    <option value="Non-Attached">Non-Attached</option>

                </select>

            </div>


            <div class="form-group">

                <label>AC Type</label>

                <select name="ac_type" required>

                    <option value="">Select AC Type</option>
                    <option value="AC">AC</option>
                    <option value="Non-AC">Non-AC</option>

                </select>

            </div>


            <div class="form-group">

                <label>Bed Type</label>

                <select name="bed_type" required>

                    <option value="">Select Bed Type</option>
                    <option value="Single Bed">Single Bed</option>
                    <option value="Double Bed">Double Bed</option>
                    <option value="No Bed">No Bed</option>

                </select>

            </div>


            <div class="form-group">

                <label>Room Capacity</label>

                <input type="number"
                       name="capacity"
                       min="1"
                       placeholder="Enter room capacity"
                       required>

            </div>


            <div class="form-group">

                <label>Room Price (₹)</label>

                <input type="number"
                       name="price"
                       min="1"
                       step="0.01"
                       placeholder="Enter room price"
                       required>

            </div>


            <button type="submit"
                    name="add_room"
                    class="submit-btn">

                ➕ Add Room

            </button>

        </form>


        <a href="rooms.php" class="back-btn">
            ← Back to Manage Rooms
        </a>

    </div>

</div>


<footer>

    <p>© 2026 Room Booking System | Admin Panel</p>

</footer>

</body>
</html>