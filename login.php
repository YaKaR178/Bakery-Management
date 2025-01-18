<!--Header-->
<?php include_once 'Style/header.php';

//session error messege if the user set a wrong input
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';
// Clear the error message after displaying it
unset($_SESSION['error_message']);?>

    <!--Login-->
    <section id="login-screen" class="login">

        <div class="wrapper" id="login">
            <form action="login_init.php" method="POST">
                <h1>Login</h1>

                <div class="input-box">
                    <input name="username" placeholder="Username" required>
                    <i class='fas fa-user-alt'></i>
                </div>

                <div class="input-box">
                    <input name="password" type="password" placeholder="Password" required>
                    <i class='fas fa-lock'></i>
                </div>

                <div class="remember">
                    <label><input type="checkbox">Remember Me</label>
                </div>

                <!-- Error Message -->
                <?php if (!empty($error_message)): ?>
                    <p style="color: red; text-align: center;"><?= htmlspecialchars($error_message) ?></p>
                <?php endif; ?>

                <div class="input-box">
                    <button type="submit" class="btn">Login</button>
                </div>


                <div class="reg">
                    <div class="register-forgot">
                        <a href="sign_up.php">Create an Account</a>
                        <a href="#">Forgot Password?</a>
                    </div>
                </div>
            </form>
        </div>
    </section>



<?php include_once 'Style/footer.php';?>