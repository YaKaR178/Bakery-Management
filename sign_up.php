<?php
/**
 * File Name: sign_up.php
 * Author: Eliasaf Yakar, Niv Zukerman and Nissan Yaar
 * Created On: 2025-01-01
 * Last Modified: 2025-01-01
 * Description:
 * This script handles user registration for the 'From The Heart Bakery' system. It provides functionality for collecting, validating, and storing user information securely in the database. Upon successful registration, users are redirected to the login page.

 * Features:
 * - **Session Management**: Begins the session to manage user state after registration.
 * - **Database Interaction**: Utilizes a database connection (`db_con.php`) to insert user details into the `users` table.
 * - **Form Handling**:
 *   - Collects and validates user input, including email, password, name, address, phone, and optional date of birth.
 *   - Combines phone prefix and number into a single phone field.
 * - **Validation**:
 *   - Ensures the email follows a valid format using PHP's `filter_var()` function.
 *   - Password and other fields are required to be properly filled in before submission.
 * - **User Experience**:
 *   - Provides alerts for success or failure in registration.
 *   - Redirects users to `login.php` upon successful registration or `sign_up.php` on failure.
 * - **Dynamic Form Design**:
 *   - Includes input fields for login and personal details.
 */
session_start();
require_once 'db_con.php';  



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and process form data
    $email = $_POST['username'];
    $password = $_POST['password'];
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $phonePrefix = $_POST['phone_prefix'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $birth_date = !empty($_POST['birth_date']) ? $_POST['birth_date'] : null;

    $fullPhone = $phonePrefix . $phone;

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }
    $conn = connectToDatabase();

    // Hash the password for security
    //$hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare and bind to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO users (username, password, first_name, last_name, address, city, phone, birth_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss",$email, $password, $firstName, $lastName, $address, $city, $fullPhone, $birth_date);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Form processing and DB insertion logic

    if ($stmt->execute()) {
        // Success message and redirect
        echo "<script>
                alert('Congrats, you are now signed up to \'From The Heart Bakery\'!');
                window.location.href = 'login.php';
              </script>";
        exit(); // Stop further script execution
    } else {
        // Error message and redirect
        echo "<script>
                alert('Error: " . $stmt->error . "');
                window.location.href = 'sign_up.php';
              </script>";
        exit(); // Stop further script execution
    }
}


    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>

<?php include "Style/header.php";?>

    <!--Login-->
    <section id="sign-up-screen" class="sign-up">

        <div class="wrapper" id="sign-up">
            <form action="sign_up.php" method="POST">
                <h1>Sign Up</h1>

                <div class="sign-up-section">
                    <h3>Login Details</h3>

                    <div class="input-box">
                        <input type="email" name="username" placeholder="Email" required>
                    </div>

                    <div class="input-box">
                        <input type="password" name="password" placeholder="Password" required>
                    </div>

                </div>

                <div class="sign-up-section">

                    <h3>Personal Details</h3>

                    <div class="input-box">
                        <input type="text" placeholder="First Name" name="first_name" required>
                    </div>

                    <div class="input-box">
                        <input type="text" placeholder="Last Name" name="last_name" required>
                    </div>

                    <div class="input-box">
                        <input type="text" placeholder="Address" name="address" required>
                    </div>

                    <div class="input-box">
                        <input type="text" placeholder="City" name="city" required>
                    </div>

                    <div class="input-box" id="phone-input">
                            <select id="phone-prefix" class="phone-prefix" name="phone_prefix" required>
                                <option value="" disabled selected>Prefix</option>
                                <option value="052">052</option>
                                <option value="053">053</option>
                                <option value="054">054</option>
                                <option value="058">058</option>
                            </select>
                            <input type="tel" name="phone" placeholder="Phone" maxlength="7" pattern="[0-9]{7}" required>

                    </div>



                    <div class="input-box">
                        <input placeholder="Date of Birth" class="textbox-n" name="birth_date" type="text" onfocus="(this.type='date')"
                               onblur="(this.type='text')">
                    </div>

                </div>


                <section id="register-button">
                    <div class="input-box" style="text-align: center; margin-top: 20px;">
                        <button type="submit" class="btn">Register</button>
                    </div>
                </section>

            </form>
        </div>


    </section>

 
   <?php include "Style/footer.php";?>
    
   <?php
   if (isset($message)) {
        echo "<script>alert('$message');</script>";
   }
   ?>
