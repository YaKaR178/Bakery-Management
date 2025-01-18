<?php
session_start();
$isLoggedIn = isset($_SESSION['username']);
$isAdmin = $_SESSION['is_admin'] ?? false;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Baking from the Heart</title>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="Style/style.css">
    
</head>

<body>

    <!--Navbar-->
    <section id="header">
        <a href="index.php"><img src="img/logo.png" class="logo" alt="logo" height="100x" width="100px"></a>

        <div>
            <ul id="navbar">
                <li><a href="index.php">Home</a></li>
                <li><a href="order.php">Order</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li>
                    <a  href="<?php echo $isLoggedIn ? ($isAdmin ? 'admin.php' : 'order.php') : 'login.php'; ?>">
                        <i class='fas fa-user-alt'></i>
                    </a>
                </li>
                <?php if ($isLoggedIn): ?>
                    <li><a href="logout.php">Logout</a></li>
                <?php endif; ?>

                <div class="cart">
                    <a href="cart.php"><i class='fas fa-shopping-cart'></i></a></li>
                    <div id="cart-amount" class="cart-amount">0</div>
                </div>

                <a href="#" id="close"><i class="fa fa-close"></i></a>
            </ul>
        </div>

        <div id="mobile">
            <a href="cart.php"><i class='fas fa-shopping-cart'></i></a>
            <i id="bar" class="fa fa-bars"></i>
        </div>

        

    </section>

    <script src="Style/navbar.js"></script>
    
</body>


