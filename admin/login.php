<?php
session_start();
include "../config/database.php";

if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit();
}

$message = "";

if (isset($_POST['admin_login'])) {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = mysqli_prepare($conn, "SELECT id, name, email, password, role FROM users WHERE email = ? AND role = 'admin'");

    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 1) {

        $admin = mysqli_fetch_assoc($result);

        if (password_verify($password, $admin['password'])) {

            session_regenerate_id(true);

            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];

            header("Location: dashboard.php");
            exit();

        } else {
            $message = "Invalid Email or Password!";
        }

    } else {
        $message = "Invalid Email or Password!";
    }

    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login - Room Booking System</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #667eea, #764ba2, #6dd5ed);
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 430px;
            background: white;
            padding: 40px 35px;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.25);
            text-align: center;
        }

        .icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 35px;
            margin: 0 auto 20px;
        }

        h2 {
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
        }

        .subtitle {
            color: #777;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .input-group {
            text-align: left;
            margin-bottom: 20px;
        }

        label {
            display: block;
            color: #444;
            font-weight: bold;
            margin-bottom: 8px;
        }

        input {
            width: 100%;
            padding: 14px;
            border: 1px solid #ddd;
            border-radius: 10px;
            outline: none;
            font-size: 15px;
            transition: 0.3s;
        }

        input:focus {
            border-color: #764ba2;
            box-shadow: 0 0 8px rgba(118,75,162,0.2);
        }

        button {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 10px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            font-size: 17px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102,126,234,0.4);
        }

        .error {
            background: #ffe5e5;
            color: #d63031;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .back-home {
            display: inline-block;
            margin-top: 25px;
            color: #764ba2;
            text-decoration: none;
            font-weight: bold;
        }

        .back-home:hover {
            color: #667eea;
        }

        .footer {
            margin-top: 25px;
            color: #999;
            font-size: 12px;
        }
    </style>
</head>

<body>

<div class="login-container">

    <div class="icon">🔐</div>

    <h2>Admin Login</h2>

    <p class="subtitle">
        Room Booking Management System
    </p>

    <?php if ($message != "") { ?>
        <div class="error">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php } ?>

    <form method="POST">

        <div class="input-group">
            <label>Email Address</label>
            <input type="email" name="email"
                   placeholder="Enter admin email" required>
        </div>

        <div class="input-group">
            <label>Password</label>
            <input type="password" name="password"
                   placeholder="Enter password" required>
        </div>

        <button type="submit" name="admin_login">
            Login as Admin
        </button>

    </form>

    <a href="../index.php" class="back-home">
        ← Back to Home
    </a>

    <div class="footer">
        © 2026 Room Booking System
    </div>

</div>

</body>
</html>