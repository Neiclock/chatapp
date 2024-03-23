<?php
// display_crmembers.php
function displayCRMembers($chatroomId, $connection) {
    $membersHtml = '';

    if ($chatroomId > 0) {
        // Prepare the statement with error handling
        if ($stmt = $connection->prepare("
            SELECT u.profile_images, u.NickName 
            FROM chat_member cm 
            JOIN users u ON cm.userid = u.user_ID 
            WHERE cm.chatroomid = ?
        ")) {
            // Bind parameters and execute with error handling
            $stmt->bind_param("i", $chatroomId);
            if ($stmt->execute()) {
                $result = $stmt->get_result();

                while ($row = $result->fetch_assoc()) {
                    $membersHtml .= "<div class='profile-container'>
                        <img src='" . htmlspecialchars($row['profile_images'], ENT_QUOTES, 'UTF-8') . "' alt='Profile Image' class='profile-img'>
                        <span class='nickname'>" . htmlspecialchars($row['NickName'], ENT_QUOTES, 'UTF-8') . "</span>
                    </div>";
                }
            } else {
                // Handle execution error (e.g., log the error, display a generic error message)
                error_log("Execute error: " . $stmt->error);
                $membersHtml .= "<p>Error loading chat room members. Please try again later.</p>";
            }
            $stmt->close();
        } else {
            // Handle statement preparation error (e.g., log the error, display a generic error message)
            error_log("Prepare statement error: " . $connection->error);
            $membersHtml .= "<p>Error loading chat room members. Please try again later.</p>";
        }
    } else {
        // Handle invalid chat room ID error
        $membersHtml .= "<p>Invalid chat room. Please select a valid chat room.</p>";
    }

    return $membersHtml;
}
?>
