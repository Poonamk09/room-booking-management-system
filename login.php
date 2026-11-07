<?php
session_start();
include "config/database.php";

$error = "";

if (isset($_SESSION['user_id'])) {
    header("Location: user_dashboard.php");
    exit();
}

if (isset($_POST['login'])) {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 1) {

        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {

            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];

            header("Location: user_dashboard.php");
            exit();

        } else {
            $error = "Incorrect password!";
        }

    } else {
        $error = "Email address not registered!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Room Booking System</title>

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
            background: rgba(255, 255, 255, 0.15);
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

        .login-section {
            min-height: 85vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
        }

        .login-card {
            width: 100%;
            max-width: 430px;
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
        }

        .login-card h1 {
            text-align: center;
            color: #5533a5;
            margin-bottom: 10px;
        }

        .login-card .subtitle {
            text-align: center;
            color: #777;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 14px;
            border: 1px solid #ddd;
            border-radius: 9px;
            font-size: 15px;
            outline: none;
        }

        .form-group input:focus {
            border-color: #764ba2;
            box-shadow: 0 0 5px rgba(118,75,162,0.3);
        }

        .login-btn {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 9px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            font-size: 17px;
            font-weight: bold;
            cursor: pointer;
        }

        .login-btn:hover {
            background: linear-gradient(135deg, #764ba2, #5533a5);
        }

        .error-message {
            background: #ffe1e1;
            color: #c62828;
            padding: 12px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 20px;
        }

        .register-text {
            text-align: center;
            margin-top: 25px;
            color: #555;
        }

        .register-text a {
            color: #764ba2;
            font-weight: bold;
            text-decoration: none;
        }

        .register-text a:hover {
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
                margin: 8px;
                display: inline-block;
            }

            .login-card {
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
        <a href="register.php">Register</a>
    </nav>
</header>

<section class="login-section">

    <div class="login-card">

        <h1>Welcome Back!</h1>

        <p class="subtitle">
            Login to your Room Booking Account
        </p>

        <?php if ($error != "") { ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php } ?>

        <form method="POST" action="">

            <div class="form-group">
                <label>Email Address</label>
                <input
                    type="email"
                    name="email"
                    placeholder="Enter your email"
                    required
                >
            </div>

            <div class="form-group">
                <label>Password</label>
                <input
                    type="password"
                    name="password"
                    placeholder="Enter your password"
                    required
                >
            </div>

            <button type="submit" name="login" class="login-btn">
                Login
            </button>

        </form>

        <p class="register-text">
            Don't have an account?
            <a href="register.php">Create Account</a>
        </p>

    </div>

</section>

<footer>
    © 2026 Room Booking System | All Rights Reserved
</footer>

</body>
</html>