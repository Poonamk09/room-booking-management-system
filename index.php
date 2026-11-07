<?php
session_start();

$is_logged_in = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html>
<head>

<title>Room Booking Management System</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>

/* GENERAL */

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

body {
    background: #f5f3ff;
}

/* HEADER */

header {
    background: linear-gradient(90deg, #1e1b4b, #6d28d9, #2563eb);
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

nav {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

nav a {
    color: white;
    text-decoration: none;
    padding: 10px 15px;
    border-radius: 20px;
    transition: 0.3s;
}

nav a:hover {
    background: #ec4899;
}

/* HERO SECTION */

.home {
    min-height: 550px;
    padding: 80px 20px;
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;

    background: linear-gradient(
        135deg,
        #312e81,
        #7c3aed,
        #db2777,
        #2563eb
    );
}

.home-content {
    max-width: 850px;
}

.home-tag {
    display: inline-block;
    background: rgba(255,255,255,0.2);
    border: 1px solid white;
    color: white;
    padding: 12px 25px;
    border-radius: 30px;
    margin-bottom: 25px;
    letter-spacing: 2px;
}

.home h1 {
    color: white;
    font-size: 52px;
    margin-bottom: 20px;
}

.home p {
    color: #fce7f3;
    font-size: 19px;
    line-height: 1.8;
    margin-bottom: 25px;
}

.home h3 {
    color: #fef08a;
    margin-bottom: 20px;
    font-size: 24px;
}

/* BUTTONS */

.home .btn {
    display: inline-block;
    background: linear-gradient(90deg, #f97316, #ec4899);
    color: white;
    text-decoration: none;
    padding: 15px 32px;
    border-radius: 30px;
    margin: 8px;
    font-weight: bold;
    transition: 0.3s;
}

.home .btn:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.2);
}

.home-register-btn {
    display: inline-block;
    padding: 13px 28px;
    border: 2px solid white;
    border-radius: 30px;
    color: white;
    text-decoration: none;
    margin: 8px;
}

/* FEATURES */

.features {
    padding: 70px 7%;
    text-align: center;
    background: linear-gradient(180deg, #fdf4ff, #eef2ff);
}

.features h2 {
    color: #4c1d95;
    font-size: 36px;
    margin-bottom: 12px;
}

.features-subtitle {
    color: #64748b;
    margin-bottom: 40px;
}

.features-container {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 25px;
}

.feature-card {
    background: white;
    padding: 35px 25px;
    border-radius: 18px;
    box-shadow: 0 8px 25px rgba(76,29,149,0.12);
    border-bottom: 5px solid #8b5cf6;
    transition: 0.3s;
}

.feature-card:nth-child(1) {
    border-bottom-color: #ec4899;
}

.feature-card:nth-child(2) {
    border-bottom-color: #06b6d4;
}

.feature-card:nth-child(3) {
    border-bottom-color: #f59e0b;
}

.feature-card:hover {
    transform: translateY(-10px);
}

.feature-icon {
    font-size: 45px;
    margin-bottom: 20px;
}

.feature-card h3 {
    color: #4c1d95;
    margin-bottom: 12px;
}

.feature-card p {
    color: #64748b;
    line-height: 1.8;
}

/* FOOTER */

footer {
    background: #1e1b4b;
    color: white;
    text-align: center;
    padding: 25px;
}

/* MOBILE */

@media(max-width: 768px) {

    header {
        flex-direction: column;
    }

    nav {
        justify-content: center;
    }

    .home h1 {
        font-size: 34px;
    }

    .features-container {
        grid-template-columns: 1fr;
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

        <?php if ($is_logged_in) { ?>

            <a href="user_dashboard.php">Dashboard</a>
            <a href="my_bookings.php">My Bookings</a>

        <?php } else { ?>

            <a href="login.php">Login</a>
            <a href="register.php">Register</a>

        <?php } ?>

    </nav>

</header>

<section class="home">

    <div class="home-content">

        <span class="home-tag">
            WELCOME TO OUR BOOKING SYSTEM
        </span>

        <h1>Find Your Perfect Stay</h1>

        <p>
            Comfortable rooms, affordable prices and easy booking.
            Book your perfect room with just a few clicks.
        </p>

        <?php if ($is_logged_in) { ?>

            <h3>
                Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!
            </h3>

            <a href="user_dashboard.php" class="btn">
                Go to Dashboard →
            </a>

        <?php } else { ?>

            <a href="rooms.php" class="btn">
                Explore Rooms →
            </a>

            <a href="register.php" class="home-register-btn">
                Create Account
            </a>

        <?php } ?>

    </div>

</section>

<section class="features">

    <h2>Why Choose Us?</h2>

    <p class="features-subtitle">
        Enjoy a simple and comfortable booking experience.
    </p>

    <div class="features-container">

        <div class="feature-card">
            <div class="feature-icon">🛏️</div>
            <h3>Comfortable Rooms</h3>
            <p>Choose from different rooms according to your needs.</p>
        </div>

        <div class="feature-card">
            <div class="feature-icon">📅</div>
            <h3>Easy Booking</h3>
            <p>Book your room easily with our simple booking process.</p>
        </div>

        <div class="feature-card">
            <div class="feature-icon">💳</div>
            <h3>Easy Payment</h3>
            <p>View your booking charges and payment details easily.</p>
        </div>

    </div>

</section>

<footer>

    <p>© 2026 Room Booking Management System | All Rights Reserved</p>

</footer>

</body>
</html>