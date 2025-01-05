<?php
/**
 * File Name: login_init.php
 * Author: Eliasaf Yakar, Niv Zukerman and Nissan Yaar
 * Created On: 2025-01-01
 * Last Modified: 2025-01-01
 * Description:
 * This script handles user login functionality for a bakery management system. It verifies user credentials against a database, initiates a session, and redirects the user based on their role (admin or regular user).
 * 
 * Features:
 * - **Session Management**: Initializes a session to store user data after successful login.
 * - **Database Connection**: Utilizes `db_con.php` to establish a connection to the database.
 * - **Input Handling**: Collects and processes username and password from a POST request.
 * - **User Authentication**: 
 *   - Verifies the provided username and password against the `users` table.
 *   - Determines if the user has admin privileges.
 * - **Role-Based Redirection**:
 *   - Admin users are redirected to `admin.php`.
 *   - Regular users are redirected to `order.php`.
 * - **Error Response**: Returns a JSON error message for incorrect credentials or database connection issues.
 */
    session_start();
    require 'db_con.php';

    // Check if form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Collect and sanitize input
        $username = $_POST['username'];
        $password = $_POST['password'];
        
        
        $conn = connectToDatabase();
        
        // Check connection
        if ($conn->connect_error) {
            die(json_encode(["status" => "error", "message" => "Database connection failed."]));
        }
    
        // Query to check user and password
        $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
        $result = $conn->query($sql);
    
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $isAdmin = $row['is_admin'];
            $_SESSION['username'] = $username;
            $_SESSION['is_admin'] = $isAdmin;
            
            if ($_SESSION['is_admin']) {
                // Redirect to manager zone
                header("Location: admin.php");
                exit();
            
            }   
            
            else {
                // Redirect to user zone or other appropriate page
                header("Location: order.php");
                exit();
            }
            
        } else {
            echo json_encode(["status" => "error", "message" => "Incorrect username or password."]);
        }
    
        // Close connection
        $conn->close();
    }
    ?>