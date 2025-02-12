<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Connect to database
    $conn = new mysqli('localhost', 'root', '', 'shop');
    
    // Check for credentials
    $username = $_POST['username'];
    $password = md5($_POST['password']); // Use hashed passwords
    
    $query = "SELECT * FROM admin WHERE username='$username' AND password='$password'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $_SESSION['admin'] = $username;
        header('Location: dashboard.php');
    } else {
        $error = 'Invalid login credentials!';
    }
}

// Array of background images
$backgroundImages = [
    '/trial/frontend/uploads/1600w-KBBZLdpjLcM.webp',
    '/trial/frontend/uploads/1078546434321809412.jpg',
    '/trial/frontend/uploads/beautiful-nature-desktop-1920x1200-wallpaper-preview.jpg',
    '/trial/frontend/uploads/e59c829455bd9cdc12930cb72c7253f3.jpg',
    '/trial/frontend/uploads/pc-wallpapers-v0-6q5bm9ch16xc1.webp',
];

// Pick a random image
$randomBackground = $backgroundImages[array_rand($backgroundImages)];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="frontend/uploads/logo1.png">
    <title>Login</title>
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: url('<?php echo $randomBackground; ?>') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Container for form */
        .login-container {
            background-color: rgba(255, 255, 255, 0.9); /* Semi-transparent white */
            padding: 30px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .login-container h1 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
        }

        .login-container input[type="text"],
        .login-container input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        .login-container button {
            width: 100%;
            padding: 10px;
            background-color: #8ac926;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .login-container button:hover {
            background-color: #6fb01c; /* Darker green on hover */
        }

        .login-container p.error {
            color: red;
            font-size: 14px;
        }

        .login-container .back-button {
            margin-top: 15px;
            display: inline-block;
            padding: 10px 20px;
            background-color: #ff5722;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .login-container .back-button:hover {
            background-color: #e64a19;
        }

        /* Responsive design for small screens */
        @media (max-width: 576px) {
            .login-container {
                padding: 20px;
            }

            .login-container h1 {
                font-size: 20px;
            }

            .login-container input[type="text"],
            .login-container input[type="password"] {
                font-size: 14px;
            }

            .login-container button,
            .login-container .back-button {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Login</h1>
        <?php if (isset($error)) { ?>
            <p class="error"><?php echo $error; ?></p>
        <?php } ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
         <!--   <h3 style="font-size: 10px; padding: 20px;">Hint: Your password is admin123</h3> -->
        </form>
        <a href="javascript:history.back()" class="back-button">Back</a>
    </div>
</body>
</html>
