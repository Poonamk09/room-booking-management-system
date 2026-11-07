<?php
session_start();
include "config/database.php";

$message = "";
$message_type = "";

if (isset($_SESSION['user_id'])) {
    header("Location: user_dashboard.php");
    exit();
}

if (isset($_POST['register'])) {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];

    if (!preg_match("/^[0-9]{10}$/", $phone)) {

        $message = "Please enter a valid 10-digit phone number!";
        $message_type = "error";

    } elseif (strlen($password) < 6) {

        $message = "Password must contain at least 6 characters!";
        $message_type = "error";

    } else {

        $check_sql = "SELECT id FROM users WHERE email = ?";
        $check_stmt = mysqli_prepare($conn, $check_sql);

        mysqli_stmt_bind_param($check_stmt, "s", $email);
        mysqli_stmt_execute($check_stmt);

        $check_result = mysqli_stmt_get_result($check_stmt);

        if (mysqli_num_rows($check_result) > 0) {

            $message = "Email address already registered!";
            $message_type = "error";

        } else {

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (name, email, phone, password)
                    VALUES (?, ?, ?, ?)";

            $stmt = mysqli_prepare($conn, $sql);

            mysqli_stmt_bind_param(
                $stmt,
                "ssss",
                $name,
                $email,
                $phone,
                $hashed_password
            );

            if (mysqli_stmt_execute($stmt)) {

                $message = "Registration Successful! You can now login.";
                $message_type = "success";

            } else {

                $message = "Registration Failed! Please try again.";
                $message_type = "error";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>

    <title>Register - Room Booking System</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea, #764ba2, #f093fb);
        }

        header {
            background: rgba(255,255,255,0.15);
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
            margin-left: 22px;
            font-weight: bold;
        }

        nav a:hover {
            color: #ffe082;
        }

        .register-section {
            min-height: 85vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
        }

        .register-card {
            width: 100%;
            max-width: 480px;
            background: white;
            padding: 38px;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
        }

        .register-card h1 {
            text-align: center;
            color: #5533a5;
            margin-bottom: 10px;
        }

        .subtitle {
            text-align: center;
            color: #777;
            margin-bottom: 28px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 13px;
            border: 1px solid #ddd;
            border-radius: 9px;
            font-size: 15px;
            outline: none;
        }

        .form-group input:focus {
            border-color: #764ba2;
            box-shadow: 0 0 5px rgba(118,75,162,0.3);
        }

        .register-btn {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 9px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            font-size: 17px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 8px;
        }

        .register-btn:hover {
            background: linear-gradient(135deg, #764ba2, #5533a5);
        }

        .message {
            padding: 12px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 20px;
        }

        .success {
            background: #d9fbe5;
            color: #18743b;
        }

        .error {
            background: #ffe1e1;
            color: #c62828;
        }

        .login-text {
            text-align: center;
            margin-top: 23px;
            color: #555;
        }

        .login-text a {
            color: #764ba2;
            font-weight: bold;
            text-decoration: none;
        }

        .login-text a:hover {
            text-decoration: underline;
        }

        footer {
            text-align: center;
            color: white;
            padding: 20px;
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

            .register-card {
                padding: 28px 22px;
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
        <a href="login.php">Login</a>
    </nav>

</header>

<section class="register-section">

    <div class="register-card">

        <h1>✨ Create Account</h1>

        <p class="subtitle">
            Register to Book Your Favourite Room
        </p>

        <?php if ($message != "") { ?>

            <div class="message <?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>

        <?php } ?>

        <form method="POST" action="">

            <div class="form-group">
                <label>Full Name</label>
                <input
                    type="text"
                    name="name"
                    placeholder="Enter your full name"
                    value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label>Email Address</label>
                <input
                    type="email"
                    name="email"
                    placeholder="Enter your email"
                    value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label>Phone Number</label>
                <input
                    type="tel"
                    name="phone"
                    placeholder="Enter 10-digit phone number"
                    pattern="[0-9]{10}"
                    maxlength="10"
                    value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label>Password</label>
                <input
                    type="password"
                    name="password"
                    placeholder="Create your password"
                    minlength="6"
                    required
                >
            </div>

            <button type="submit" name="register" class="register-btn">
                Create Account →
            </button>

        </form>

        <p class="login-text">
            Already have an account?
            <a href="login.php">Login Here</a>
        </p>

    </div>

</section>

<footer>
    © 2026 Room Booking System | All Rights Reserved
</footer>

</body>
</html>