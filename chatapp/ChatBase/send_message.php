<?php
//send_message.php
require_once 'db_connect.php';

session_start();

if (isset($_SESSION['user_id'], $_POST['message'], $_POST['chatroomid'])) {
    $userId = $_SESSION['user_id'];
    $chatroomId = filter_input(INPUT_POST, 'chatroomid', FILTER_VALIDATE_INT);
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);
    
    // Further validation and authorization checks here...

    if (!$chatroomId || !$message) {
        echo 'error: invalid input';
        exit;
    }

    $stmt = $connection->prepare("INSERT INTO chat (userid, chatroomid, message, chat_date) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iis", $userId, $chatroomId, $message);
    
    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error: failed to send message';
    }

    $stmt->close();
} else {
    echo 'error: missing required fields';
}

?>
