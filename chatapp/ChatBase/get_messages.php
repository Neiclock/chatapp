<?php
require_once 'db_connect.php';
session_start();

$chatroomId = isset($_GET['chatroomid']) ? intval($_GET['chatroomid']) : 0;

if ($chatroomId <= 0) {
    echo "Invalid chatroom ID.";
    exit; // Ensure script stops execution if chatroom ID is not valid
}

if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to view messages.";
    exit; // Ensure script stops execution if the user is not logged in
}

// Validate chatroom membership here
// This is a placeholder for actual validation logic
$is_member = true; // Assume a function checks if the user is part of the chatroom
if (!$is_member) {
    echo "Access denied. You are not a member of this chatroom.";
    exit;
}

if ($stmt = $connection->prepare("SELECT users.NickName, chat.message, chat.chat_date FROM chat JOIN users ON chat.userid = users.user_ID WHERE chatroomid = ? ORDER BY chat.chat_date ASC")) {
    $stmt->bind_param("i", $chatroomId);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $messages = '';
        while ($row = $result->fetch_assoc()) {
            $messages .= "<div class='message'><strong>" . htmlspecialchars($row['NickName']) . ":</strong> " . htmlspecialchars($row['message']) . " <span class='date'>" . htmlspecialchars($row['chat_date']) . "</span></div>";
        }
        echo $messages;
    } else {
        echo "An error occurred while fetching messages.";
    }
    $stmt->close();
} else {
    echo "An error occurred preparing the message retrieval.";
}
?>
