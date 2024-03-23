<?php
// Assuming db_connect.php establishes database connection available as $connection
//cookies.php
// Function to validate login cookie and set session
function validateLoginCookie($loginId, $connection) {
    $stmt = $connection->prepare("SELECT user_ID, NickName, profile_images FROM users WHERE Login_ID = ?");
    $stmt->bind_param("s", $loginId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        // Set session variables based on user details
        $_SESSION['user_id'] = $user['user_ID'];
        $_SESSION['nickname'] = $user['NickName'];
        $_SESSION['profile_image'] = $user['profile_images']; // Assuming you want to store this in session
        return true;
    }
    return false;
}

// Check if session is not set but login cookie exists
if (!isset($_SESSION['user_id']) && isset($_COOKIE['login_id'])) {
    require_once 'db_connect.php'; // Ensure database connection is available
    if (!validateLoginCookie($_COOKIE['login_id'], $connection)) {
        // Cookie validation failed; handle according to your application's logic
        // For example: redirect to login page or show an error message
        header('Location: index.php'); // Redirect to login page
        exit;
    }
}
?>
