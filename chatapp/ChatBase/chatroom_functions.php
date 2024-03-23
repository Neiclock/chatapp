<?php
// Function to fetch chat room members with pagination
function fetch_chat_room_members($connection, $chatroomId, $users_per_page, $offset) {
    $stmt = $connection->prepare("
        SELECT u.NickName, u.profile_images
        FROM chat_member cm
        JOIN users u ON cm.userid = u.user_ID
        WHERE cm.chatroomid = ?
        LIMIT ? OFFSET ?
    ");
    $stmt->bind_param("iii", $chatroomId, $users_per_page, $offset);
    $stmt->execute();
    $result = $stmt->get_result();

    $membersHtml = '';
    while ($row = $result->fetch_assoc()) {
        $membersHtml .= "<div class='profile-container'>";
        $membersHtml .= "<img src='" . htmlspecialchars($row['profile_images']) . "' alt='Profile Image' class='profile-img'>";
        $membersHtml .= "<span class='nickname'>" . htmlspecialchars($row['NickName']) . "</span>";
        $membersHtml .= "</div>";
    }

    $stmt->close();
    return $membersHtml;
}

// Function to calculate total pages for chat room members
function calculate_total_pages($connection, $chatroomId, $users_per_page) {
    $stmt = $connection->prepare("SELECT COUNT(*) as total FROM chat_member WHERE chatroomid = ?");
    $stmt->bind_param("i", $chatroomId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $total_pages = ceil($row['total'] / $users_per_page);
    $stmt->close();
    return $total_pages;
}
?>
