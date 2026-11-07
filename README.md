# Room Booking Management System

## Project Overview
A web-based Room Booking Management System developed using PHP and MySQL. It allows users to register, log in, view available rooms, book rooms, select food items, make demo payments, and manage their bookings.

## Technologies Used
- HTML
- CSS
- JavaScript
- PHP
- MySQL
- XAMPP
- Visual Studio Code

## Features

### User Module
- User Registration and Login
- View Available Rooms
- Room Booking
- Food Item Selection
- Payment Demonstration
- Booking Confirmation
- View My Bookings
- Cancel Booking

### Admin Module
- Admin Login
- Admin Dashboard
- Manage Rooms
- Manage Users
- Manage Bookings
- Manage Payments
- Manage Food Items

## Project Structure
```text
room_booking/
│
├── admin/
├── config/
├── css/
├── database/
│   └── room_booking.sql
├── images/
├── includes/
├── screenshots/
├── index.php
├── rooms.php
├── booking.php
├── payment.php
├── login.php
├── register.php
└── README.md
```

## Installation Steps

1. Install XAMPP.
2. Copy the project folder into `C:\xampp\htdocs\`.
3. Start Apache and MySQL from XAMPP Control Panel.
4. Open phpMyAdmin.
5. Create a database named `room_booking`.
6. Select the database and click on Import.
7. Choose `database/room_booking.sql` and click Import/Go.
8. Configure your local database credentials in `config/database.php`.
9. Open the following URL in your browser:

   `http://localhost/room_booking/`

## Screenshots
Project screenshots are available in the `screenshots/` folder.

## Payment Information
This project contains a payment demonstration feature. It does not use a real payment gateway.

## Developer
**Poonam Kumbhar**

B.Tech Computer Science and Engineering

## Purpose
Developed for academic and educational purposes.