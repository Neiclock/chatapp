<?php
//chatroom.php
// Include the database connection script
require_once 'db_connect.php';
require_once 'get_chat_title.php';
require_once 'display_crmembers.php'; 
require_once 'chatroom_functions.php';


// Start the session
session_start();

require_once 'cookies.php';


$chatroomId = isset($_GET['chatroomid']) ? intval($_GET['chatroomid']) : 0;
$chatTitle = ""; // Variable to store the chat title

if ($chatroomId > 0) {
    $chatTitle = getChatTitle($chatroomId, $connection);
}


$user_id = $_SESSION['user_id'] ?? null;
$userNickName = 'Guest'; // Default values
$profileImage = 'default.png';

if ($user_id) {
    $stmt = $connection->prepare("SELECT NickName, profile_images FROM users WHERE user_ID = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($user = $result->fetch_assoc()) {
        $userNickName = $user['NickName']; // Use the fetched NickName
        $profileImage = $user['profile_images']; // Use the fetched profile image
    }
    $stmt->close();
}

$chatRoomMembersHtml = displayCRMembers($chatroomId, $connection);

$users_per_page = 4; // Display four users per page
$currentPage = $_GET['page'] ?? 1; // Get the current page from the URL parameter, default is 1
$offset = ($currentPage - 1) * $users_per_page;

// Call the functions from the included file
$chatRoomMembersHtml = fetch_chat_room_members($connection, $chatroomId, $users_per_page, $offset);
$totalPages = calculate_total_pages($connection, $chatroomId, $users_per_page);

// ... HTML and other PHP code ...

// Pagination Controls
$prevPage = max(1, $currentPage - 1);
$nextPage = min($totalPages, $currentPage + 1);


if (isset($_GET['logout']) && $_GET['logout'] == '1') {
    // Destroy the session and redirect to login page
    session_destroy();
    header('Location: index.php');
    exit;
}


?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat System Main Page</title>
    <link rel="stylesheet" href="css/chatroom.css">
</head>

<body>

    <!-- Top Black Bar -->
    <div class="top_bar">
        <div class="user_info">
            <?php if ($user_id): ?>
            <img src="<?php echo htmlspecialchars($user['profile_images'], ENT_QUOTES, 'UTF-8'); ?>" alt="Profile Image"
                class="profile_img">
            <div class="dropdown">
                <button
                    class="nickname"><?php echo htmlspecialchars($user['NickName'], ENT_QUOTES, 'UTF-8'); ?></button>
                <div class="dropdown_content">
                    <a href="?logout=1">Log out</a>
                </div>
            </div>
            <?php else: ?>
            <img src="<?php echo $profileImage; ?>" alt="Profile Image" class="profile_img">
            <p class="nickname">Guest</p>
            <?php endif; ?>
        </div>
        <!-- Lobby Button -->
        <div class="lobby_button" onclick="location.href='main.php';">
            <img src="lobby.png" alt="Lobby Icon"> <!-- Replace with your lobby icon path -->
            <span>Lobby</span>
        </div>
    </div>


    <div class="container">
        <div class="my-chat-rooms">
            <h1>Chat_Rooms_Member</h1>
            <div class="chat-group-header">
                <?php echo $chatRoomMembersHtml; ?>
            </div>
            <div class="pagination-controls">
                <span>Page <?php echo $currentPage; ?> of <?php echo $totalPages; ?></span>
                <a href="?chatroomid=<?php echo $chatroomId; ?>&page=<?php echo $prevPage; ?>"
                    class="prev-button">Previous</a>
                <a href="?chatroomid=<?php echo $chatroomId; ?>&page=<?php echo $nextPage; ?>"
                    class="next-button">Next</a>
            </div>
        </div>


        <!-- Chat groups -->

        <div class="chat-room-list">
            <div class="list-header">
                <h1>Chat_Title: <?php echo htmlspecialchars($chatTitle); ?> </h1>

                <div class="add-chat-group">
                    
                    <button onclick="location.href='main.php';">Leave</button>
                </div>
            </div>
            <div id="message-display">
                <!-- Messages will be displayed here -->

            </div>
            <div id="message-input-area">
                <input type="text" id="message-input" placeholder="Type your message here...">
                <button id="send-message">Send</button> <!-- Linked to JavaScript -->
            </div>
        </div>

    </div>


    <script src="chatroom.js"></script>
</body>

</html>