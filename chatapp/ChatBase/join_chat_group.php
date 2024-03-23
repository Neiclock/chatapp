<?php
// join_chat_group.php

// Include the database connection script
require_once 'db_connect.php';

// Start the session
session_start();

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['chatroomid'])) {
    $chatroom_id = $_POST['chatroomid'];
    $user_id = $_SESSION['user_id'] ?? null;

    if ($user_id) {
        // Insert a new record into the chat_member table using INSERT IGNORE
        $stmt = $connection->prepare("INSERT IGNORE INTO chat_member (chatroomid, userid) VALUES (?, ?)");
        $stmt->bind_param("ii", $chatroom_id, $user_id);

        if ($stmt->execute()) {
            // Redirect to checkroom.php with a success message
            $stmt->close();
            header('Location: chatroom.php?joined=1&chatroomid=' . $chatroom_id);
            exit;
        } else {
            // Handle error scenario
            $stmt->close();
            header('Location: chatroom.php?error=joinfailed');
            exit;
        }
    } else {
        // Redirect to the login page if the user is not logged in
        header('Location: index.php?error=notloggedin');
        exit;
    }
}
?>
