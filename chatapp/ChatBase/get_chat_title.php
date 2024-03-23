<?php
// get_chat_title.php

function getChatTitle($chatroomId, $connection) {
    $title = "Chat Room"; // Default title if none found or on error

    // Check if the $chatroomId is valid
    if($chatroomId <= 0) {
        // Log the error or handle it as per your application's error handling policy
        error_log("Invalid chatroom ID: " . $chatroomId);
        return $title; // Return the default title
    }

    if ($stmt = $connection->prepare("SELECT chat_title FROM chatroom WHERE chatroomid = ?")) {
        $stmt->bind_param("i", $chatroomId);
        
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $title = $row['chat_title'];
            }
        } else {
            // Log error details for debugging
            error_log("Failed to execute query: " . $stmt->error);
        }
        $stmt->close();
    } else {
        // Log error details for debugging
        error_log("Failed to prepare statement: " . $connection->error);
    }

    // Note: Ensure $title is sanitized before being output to HTML to prevent XSS
    return htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
}
?>
