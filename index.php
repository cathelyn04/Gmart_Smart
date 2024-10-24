<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"> <!-- Font Awesome CDN -->
    <title>Welcome to Gmart Smart</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('assets/images/Website/stall.jpg'); /* Set the background image */
            background-size: cover; /* Make the image cover the entire background */
            background-position: center; /* Center the image */
            background-repeat: no-repeat; /* Prevent the image from repeating */
            display: flex;
            flex-direction: column;
            min-height: 125vh;
        }

        header {
            background-color: #333; 
            color: white;
            padding: 50px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2); /* Add shadow for depth */
        }

        header h1 {
            margin: 0;
            font-size: 2.5em; /* Increase header font size */
        }

        header img.logo {
            width: 800px; /* Increased logo size */
            height: auto; /* Maintain aspect ratio */
            vertical-align: middle;
            margin-right: 10px;
        }

        nav {
            margin: 20px 0;
            display: flex;
            justify-content: center;
            flex-wrap: wrap; /* Allow wrapping of nav items */
        }

        .nav-section {
            margin: 10px; /* Space between sections */
            border: 1px solid #ccc; /* Border for sections */
            border-radius: 8px; /* Rounded corners */
            background-color: rgba(255, 255, 255, 0.9); /* White background with slight transparency */
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2); /* Stronger shadow effect */
            padding: 20px; /* Padding for sections */
            width: 220px; /* Fixed width for nav sections */
            transition: transform 0.2s; /* Add transition for hover effect */
        }

        .nav-section:hover {
            transform: translateY(-5px); /* Raise sections on hover */
        }

        nav h2 {
            text-align: center; /* Center align section titles */
            margin: 0 0 10px 0; /* Space below title */
            font-size: 1.5em; /* Increase section title font size */
        }

        nav ul {
            list-style-type: none;
            padding: 0;
            text-align: center; /* Center align list items */
        }

        nav ul li {
            margin: 10px 0; /* Space between buttons */
        }

        nav a {
            display: inline-block;
            padding: 12px 20px; /* Adjust button padding */
            background-color: #FFD700; /* Gold background for buttons */
            color: #333;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s, color 0.3s;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15); /* Add shadow to buttons */
        }

        nav a:hover {
            background-color: #FFC107; /* Darker yellow on hover */
            color: white;
        }

        main {
            padding: 20px;
            text-align: center;
            flex: 1; /* Allow main to grow and fill space */
        }

        section {
            background-color: rgba(255, 255, 255, 0.8); /* Slightly transparent white */
            padding: 30px; /* Increased padding */
            border-radius: 12px; /* More rounded corners */
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3); /* Stronger shadow */
            max-width: 600px; /* Max width for the section */
            margin: 20px auto; /* Center the section */
        }

        section h2 {
            font-size: 1.8em; /* Increase About Us title size */
            margin-bottom: 15px; /* Space below title */
            color: #333; /* Darker title color */
        }

        section p {
            font-size: 1.1em; /* Increase paragraph font size */
            color: blue; /* Blue text for paragraph */
            line-height: 1.5; /* Improve line spacing */
        }

        footer {
            text-align: center;
            padding: 10px;
            background-color: #333;
            color: white;
            position: relative;
            bottom: 0;
            width: 100%;
            margin-top: 20px; /* Space above footer */
        }
    </style>
</head>
<body>
    <header style="text-align: center;">
        <h1 style="display: inline-block; white-space: nowrap;">
            <img src="assets/images/website/logo.jpg" class="logo" alt="Gmart Smart Logo" 
                style="width: 109px !important; height: auto !important; vertical-align: middle;">
                    
            <span style="font-size: 45px; margin-right:190px; vertical-align: middle;">Welcome to Gmart Smart</span>
        </h1>
    </header>


    <nav>
        <div class="nav-section">
            <h2>Admin</h2>
            <ul>
                <li><a href="admin/login.php">Admin Login</a></li>
            </ul>
        </div>
        <div class="nav-section">
            <h2>User</h2>
            <ul>
                <li><a href="user/login.php">User Login</a></li>
            </ul>
        </div>
    </nav>

    <main>
        <section>
            <h2>About Us</h2>
            <p>Gmart Smart is your one-stop shop for all your grocery needs.</p>
        </section>

        <section>
            <h2>Contact Us</h2>
            <p>
                <i class="fas fa-map-marker-alt"></i> 
                <strong>Location:</strong> Jalan Kg Sudoh - Kg Apar, 94000 Bau, Sarawak
            </p>
            <p>
                <i class="fab fa-whatsapp"></i> 
                <strong>Phone:</strong> <a href="https://wa.me/60162681540" style="color: blue;">0162681540</a>
            </p>
        </section>
    </main>

    <footer>
        <p style="color: white;">&copy; 2024 Gmart Smart. All rights reserved.</p>
    </footer>
</body>
</html>
