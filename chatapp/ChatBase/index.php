<?php
session_start(); // Start a new session or resume the existing one


// Initialize CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Initialize the login attempts session variables if not already set
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['last_attempt_time'] = time();
}

// Check if the number of login attempts is greater than the allowed limit
if ($_SESSION['login_attempts'] > 10 && time() - $_SESSION['last_attempt_time'] < 30) {
    die("Too many login attempts. Please try again later.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // CSRF token validation
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('CSRF token mismatch.');
    }
}


// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login_id']) && isset($_POST['password'])) {
    require_once 'db_connect.php'; // Include the database connection script

    // Collect login details from the form
    $login_id = $_POST['login_id'];
    $password = $_POST['password'];

    // Prepare the SQL statement to prevent SQL injection
    $stmt = $connection->prepare("SELECT * FROM users WHERE Login_ID = ?");
    $stmt->bind_param("s", $login_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch the user's data
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['Password'])) {
            // Store user info in session variables
            $_SESSION['user_id'] = $user['user_ID'];
            $_SESSION['login_id'] = $user['Login_ID'];
            $_SESSION['nickname'] = $user['NickName'];
            $_SESSION['login_attempts'] = 0; // Reset the login attempts on successful login
        
            // Set a login cookie valid for 30 days
            setcookie('login_id', $login_id, time() + (86400 * 30), "/"); // 86400 = 1 day
        
            // Redirect to the main.php page
            header("Location: main.php");
            exit();
        }else {
            // Invalid password
            // Increment the login attempts counter on password mismatch
            $_SESSION['login_attempts'] += 1;
            $_SESSION['last_attempt_time'] = time();
            $error_message = 'Invalid login ID or password.';
        }
    } else {
        // No user found with the provided Login_ID
        // Increment the login attempts counter if no user is found
        $_SESSION['login_attempts'] += 1;
        $_SESSION['last_attempt_time'] = time();
        $error_message = 'Invalid login ID or password.';
    }
    
    // Close the statement and the connection
    $stmt->close();
    $connection->close();
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="css/index.css">
    <style>
        .container {
            display: flex;
            justify-content: space-between;
        }
        
        .container .advertisement {
            flex-basis: 30%;
            margin: 0 20px;
        }
        
        .container .login-form {
            flex-basis: 60%;
        }
        
        .advertisement img {
            max-width: 100%;
            max-height: 100%;
            width: auto;
            height: 400px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="advertisement">
            <img src="adv.png" alt="Left Ad">
        </div>
        
        <div class="login-form">
            <h1>Login</h1>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                <label for="login_id">Login_ID:</label>
                <input type="text" name="login_id" id="login_id" required><br>

                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required><br>

                <button type="submit">Login</button>
                
            </form>
            <p>Not registered yet? <a href="register.php">Register here</a></p>

            <?php if (isset($error_message)): ?>
                <p class="error"><?= htmlspecialchars($error_message) ?></p>
            <?php endif; ?>
        </div>
        
        <div class="advertisement">
            <img src="adv.png" alt="Right Ad">
        </div>
    </div>
</body>
</html>
