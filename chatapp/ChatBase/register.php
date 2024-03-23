<?php
// Include the database connection script
$errorMessage = '';
$successMessage = '';

require_once 'db_connect.php';

// Start or resume a session
session_start();

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Function to generate a unique file name
function generateUniqueFileName($directory, $extension) {
    do {
        $uniqueName = md5(uniqid(rand(), true)) . '.' . $extension;
        $fullPath = $directory . '/' . $uniqueName;
    } while (file_exists($fullPath));
    return $uniqueName;
}

// Define the path to the directory where the images will be saved
$uploadDirectory = 'uploads';

// Make sure the uploads directory exists, if not, create it
if (!file_exists($uploadDirectory)) {
    mkdir($uploadDirectory, 0777, true);
}

// Rate Limiting: Only allow 10 registration attempts per 30 minutes
if (!isset($_SESSION['register_attempts'])) {
    $_SESSION['register_attempts'] = 0;
    $_SESSION['register_last_attempt_time'] = time();
} elseif ($_SESSION['register_attempts'] > 50 && (time() - $_SESSION['register_last_attempt_time']) < 1800) {
    die("Too many registration attempts. Please try again later.");
}

// Check if the registration form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect registration details
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $errorMessage = 'CSRF token mismatch. Please try again.';
        // Consider redirecting back to the registration form or showing an error
    } else {
        $login_id = $_POST['login_id'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $nickname = $_POST['nickname'];
        $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
        
        if (!$email) {
            $errorMessage = 'Invalid email format.';
        }

        // Initialize $profileImagePath with a default value or leave it null
        // If your database allows null values for 'profile_images', you can initialize this as null
        // Otherwise, set a default image path that exists for every user, e.g., 'path/to/default/image.png'
        $profileImagePath = null; // or 'path/to/default/image.png' if you don't want to allow null values

        // Handle file upload
        if (isset($_FILES['profileImage']) && $_FILES['profileImage']['error'] === UPLOAD_ERR_OK) {
            // Get file info
            $fileTmpPath = $_FILES['profileImage']['tmp_name'];
            $fileName = $_FILES['profileImage']['name'];
            $fileSize = $_FILES['profileImage']['size'];
            $fileType = $_FILES['profileImage']['type'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            // Check if file type is an image
            $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');
            if (in_array($fileExtension, $allowedfileExtensions)) {
                // Generate a unique file name and move the file
                $newFileName = generateUniqueFileName($uploadDirectory, $fileExtension);
                $dest_path = $uploadDirectory . '/' . $newFileName;
                if(move_uploaded_file($fileTmpPath, $dest_path)) {
                    // File is successfully saved
                    $profileImagePath = $dest_path;
                } else {
                    $errorMessage = 'Error moving the file';
                }
            } else {
                $errorMessage = 'Upload failed. Allowed file types: jpg, gif, png, jpeg';
            }
        } else {
            // If no file is uploaded and it's not mandatory, you might set a default image path here
            // Only do this if your logic allows for a default image in case of no upload
            // $profileImagePath = 'path/to/default/image.png';
        }

        // Proceed with user registration only if there's no error message set
        if (empty($errorMessage)) {
            // Check if the Login ID or Nickname already exists
            $check_query = $connection->prepare("SELECT * FROM users WHERE Login_ID = ? OR NickName = ?");
            $check_query->bind_param("ss", $login_id, $nickname);
            $check_query->execute();
            $result = $check_query->get_result();

            if ($result->num_rows > 0) {
                $errorMessage = "Error: Login ID or NickName already exists!";
            } else {
                // Prepare the SQL statement to prevent SQL injection
                $stmt = $connection->prepare("INSERT INTO users(Login_ID, Password, NickName, Email, profile_images) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssss", $login_id, $password, $nickname, $email, $profileImagePath); // 'sssss' denotes the types of the columns

                // Execute the prepared statement
                if ($stmt->execute()) {
                    $successMessage = "Registration successful!";
                } else {
                    $errorMessage = "Error: " . $stmt->error;
                }
                // Close the statement
                $stmt->close();
            }
            // Close the result set and the check_query statement
            $result->close();
            $check_query->close();
        } else {
            // Handle cases where there was an error before attempting to insert into the database
            // This part is crucial for not attempting database insertion when there's an upload error
        }

        // Increment the register attempts counter
        $_SESSION['register_attempts']++;
        $_SESSION['register_last_attempt_time'] = time();
    }
}

// Close the connection
$connection->close();
?>


<!DOCTYPE html>
<html>
<head>
    <title>Registration</title>
    <link rel="stylesheet" type="text/css" href="css/register.css">
</head>
<body>
    <div class="container">
        <h1>Register</h1>
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

            <label for="login_id">Login ID:</label>
            <input type="text" id="login_id" name="login_id" required><br>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br>

            <label for="nickname">NickName:</label>
            <input type="text" id="nickname" name="nickname" required><br>

            <label for="email">Email:</label>
            <input type="text" id="email" name="email" required><br>

            <label for="profileImage">ProfileImage:</label>
            <input type="file" name="profileImage" accept="image/*" required>

            <button type="submit">Register</button>

        </form>
         
         <br><br><button type="submit" id="backButton" onclick="location.href='index.php'" >Back to Login Page</button>
         <?php
            // Check for error messages in the query parameters
            if (isset($_GET['error']) && $_GET['error'] == 'invalidemail') {
                echo '<p class="error">Wrong email format. Please include an @ in the email address.</p>';
            }
         ?>
        <?php if (!empty($errorMessage)): ?>
            <p class="error"><?php echo htmlspecialchars($errorMessage); ?></p>
        <?php endif; ?>
<!-- Display success message if any -->
        <?php if (!empty($successMessage)): ?>
            <p class="success"><?php echo htmlspecialchars($successMessage); ?></p>
        <?php endif; ?>
    </div>
</body>
</html>