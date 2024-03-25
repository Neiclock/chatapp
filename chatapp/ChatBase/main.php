<?php
//main.php
// Include the database connection script
require_once 'db_connect.php';
require_once 'page_num.php'; // The pagination functions
require_once 'cookies.php';

// Start the session
session_start();

// Generate CSRF token and store it in session if not already set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}


if (isset($_GET['logout']) && $_GET['logout'] == '1') {
    // Destroy the session and redirect to login page
    session_destroy();
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'] ?? null;
$userNickName = 'Guest'; // Default values
$profileImage = 'default.png';

$my_total_pages = calculate_my_total_pages($connection, $user_id);
$list_total_pages = calculate_list_total_pages($connection);

// Determine the current page for each section
$my_current_page = isset($_GET['my_page']) ? max((int)$_GET['my_page'], 1) : 1;
$list_current_page = isset($_GET['list_page']) ? max((int)$_GET['list_page'], 1) : 1;


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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Assuming you have a way to determine if this is a create chat group POST request
    if (isset($_POST['chat_title'], $_POST['description'])) {
        // CSRF token validation
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            die('CSRF token mismatch. Please try again.');
        }

        $chat_title = $_POST['chat_title'];
        $description = $_POST['description'];
        $userid = $user_id; // Assume $user_id contains the ID of the current user

        // Prepare statement to insert new chat group
        $stmt = $connection->prepare("INSERT INTO chatroom (chat_title, description, userid) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $chat_title, $description, $userid);

        if ($stmt->execute()) {
            echo "New chat group created successfully";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }
    // Add other POST request handling (e.g., leaving a chat group) below
}



// Fetch all chat groups
$chat_groups = [];
$query = "SELECT chatroomid, chat_title, description FROM chatroom";
$result = $connection->query($query);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $chat_groups[] = $row;
    }
}

// Fetch chat groups the user has joined with a limit and offset for 'My Chat Rooms'
$my_offset = ($my_current_page - 1) * $items_per_page;
$my_chat_groups_query = "
    SELECT c.chatroomid, c.chat_title, c.description
    FROM chatroom c
    JOIN chat_member m ON c.chatroomid = m.chatroomid
    WHERE m.userid = ?
    LIMIT ?
    OFFSET ?";
$my_stmt = $connection->prepare($my_chat_groups_query);
$my_stmt->bind_param("iii", $user_id, $items_per_page, $my_offset);
$my_stmt->execute();
$result = $my_stmt->get_result();

$my_chat_groups = [];
while ($row = $result->fetch_assoc()) {
    $my_chat_groups[] = $row;
}
$my_stmt->close();

// Fetch 'List of Chat Rooms' with a limit and offset for pagination
$list_offset = ($list_current_page - 1) * $items_per_page;
$list_chat_groups_query = "SELECT chatroomid, chat_title, description FROM chatroom LIMIT ? OFFSET ?";
$list_stmt = $connection->prepare($list_chat_groups_query);
$list_stmt->bind_param("ii", $items_per_page, $list_offset);
$list_stmt->execute();
$result = $list_stmt->get_result();

$chat_groups = [];
while ($row = $result->fetch_assoc()) {
    $chat_groups[] = $row;
}
$list_stmt->close();


// Check if the user wants to leave a chat group
if (isset($_POST['leave']) && isset($_POST['chatroomid'])) {
    $chatroom_id = $_POST['chatroomid'];
    
    $stmt = $connection->prepare("DELETE FROM chat_member WHERE chatroomid = ? AND userid = ?");
    $stmt->bind_param("ii", $chatroom_id, $user_id);

    if ($stmt->execute()) {
        // Redirect back to 'My Chat Groups' page with a success message
        $stmt->close(); // Close the statement before redirecting
        header('Location: main.php?left=1');
        exit;
    } else {
        // Redirect back with an error message
        $stmt->close(); // Close the statement before redirecting
        header('Location: main.php?error=1');
        exit;
    }
}

// Same logic for $my_chat_groups but with additional WHERE condition for the specific user

$connection->close();

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat System Main Page</title>
    <link rel="stylesheet" href="css/main.css">
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
        <!-- Top Banner Ad -->
        <div class="banner_ad_top">
            <a>
                <img src="adv2.png" alt="Top Ad Image">
            </a>
        </div>
        <!-- Lobby Button -->
        <div class="lobby_button" onclick="location.href='main.php';">
            <img src="lobby.png" alt="Lobby Icon"> <!-- Replace with your lobby icon path -->
            <span>Lobby</span>
        </div>
        
    </div>

    

    <div class="container">
        <div class="my-chat-rooms">
            <h1>My Chat Rooms</h1>
            <div class="chat-group-header">
                <div>Chat Title</div>
                <div>Description</div>
                <div>Leave_button:</div>
            </div>
            <!-- Chat groups -->
            <?php foreach ($my_chat_groups as $group): ?>
            <div class="chat-group">
                <div><?php echo htmlspecialchars($group['chat_title'], ENT_QUOTES, 'UTF-8'); ?></div>
                <div><?php echo htmlspecialchars($group['description'], ENT_QUOTES, 'UTF-8'); ?></div>
                <!-- The button's div will be automatically created by the form -->
                <form method="post" action="">
                    <input type="hidden" name="chatroomid" value="<?php echo $group['chatroomid']; ?>">
                    <button type="submit" name="leave" class="leave-button">Leave</button>
                </form>
            </div>
            <?php endforeach; ?>
            <div class="navigation">
                <span class="current-page">Page <?php echo $my_current_page; ?> of <?php echo $my_total_pages; ?></span>
                <a href="?my_page=<?php echo max(1, $my_current_page - 1); ?>&list_page=<?php echo $list_current_page; ?>"
                    class="<?php echo ($my_current_page <= 1) ? 'disabled' : ''; ?>">Previous</a>
                <a href="?my_page=<?php echo ($my_current_page < $my_total_pages) ? $my_current_page + 1 : $my_current_page; ?>&list_page=<?php echo $list_current_page; ?>"
                    class="<?php echo ($my_current_page < $my_total_pages) ? '' : 'disabled'; ?>">Next</a>

            </div>

        </div>

        <div class="chat-room-list ">
            <div class="list-header">
                <h1>List of Chat Rooms</h1>
                <div class="add-chat-group">
                    <button id="btnCreateChatGroup" onclick="showCreateGroupModal()">Create Chat Group</button>
                </div>
            </div>
            <div class="chat-group-header">
                <div>Chat Title</div>
                <div>Description</div>
                <div>Join_button:</div>
            </div>
            <?php foreach ($chat_groups as $group): ?>
            <div class="chat-group">
                <h2><?php echo htmlspecialchars($group['chat_title'], ENT_QUOTES, 'UTF-8'); ?></h2>
                <p><?php echo htmlspecialchars($group['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                <form method="post" action="join_chat_group.php">
                    <input type="hidden" name="chatroomid" value="<?php echo $group['chatroomid']; ?>">
                    <button type="submit" name="join" class="join_button">Join</button>
                </form>
            </div>
            <?php endforeach; ?>
            <div class="navigation">
                <span class="current-page">Page <?php echo $list_current_page; ?> of
                    <?php echo $list_total_pages; ?></span>
                <a href="?list_page=<?php echo max(1, $list_current_page - 1); ?>&my_page=<?php echo $my_current_page; ?>"
                    class="<?php echo ($list_current_page <= 1) ? 'disabled' : ''; ?>">Previous</a>
                <a href="?list_page=<?php echo ($list_current_page < $list_total_pages) ? $list_current_page + 1 : $list_total_pages; ?>&my_page=<?php echo $my_current_page; ?>"
                    class="<?php echo ($list_current_page >= $list_total_pages) ? 'disabled' : ''; ?>">Next</a>
            </div>

        </div>


        <div id="createGroupModal" class="modal" style="display: none;">
            <!-- Modal content -->
            <div class="modal-content">
                <div class="chat-form-container">
                    <span class="close" onclick="hideCreateGroupModal()">&times;</span>
                    <form id="createChatGroupForm" method="post" action="">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                        <label for="chat_title">Chat Room Title:</label>
                        <div class="chat-form-group">
                            <input type="text" id="chat_title" name="chat_title" required>
                            <label for="description">Description:</label>
                        </div>
                        <div class="chat-form-group">
                            <textarea id="description" name="description" required></textarea>
                        </div>
                        <input type="submit" value="Create" class="chat-create-button">
                    </form>
                </div>
            </div>

            
        </div>



        <script>
        function showCreateGroupModal() {
            document.getElementById('createGroupModal').style.display = 'block';
        }

        function hideCreateGroupModal() {
            document.getElementById('createGroupModal').style.display = 'none';
        }

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            let modal = document.getElementById('createGroupModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
        </script>

</body>

</html>