<?php
// Database connection details
$host = "localhost"; 
$user = "root";      
$pass = "";          
$dbname = "navsclubs"; 

// Connect to database
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Debug: Check if the form is actually submitting
    if (empty($_POST["email"]) || empty($_POST["password"]) || empty($_POST["fullname"])) {
        die("Error: Missing required fields.");
    }

    // Get form data
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST["password"]);
    $confirm_password = trim($_POST["confirm_password"]);
    $fullname = htmlspecialchars(trim($_POST["fullname"]));
    $nickname = isset($_POST["nickname"]) ? htmlspecialchars(trim($_POST["nickname"])) : NULL;
    $rank = isset($_POST["rank"]) ? htmlspecialchars(trim($_POST["rank"])) : NULL;
    $confirmed = isset($_POST["confirmed"]) ? 1 : 0;
    $usertype = "user"; 
    $created_at = date("Y-m-d H:i:s"); 

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Error: Invalid email format.");
    }

    // Validate passwords match
    if ($password !== $confirm_password) {
        die("Error: Passwords do not match.");
    }

    // Hash password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Check if email already exists
    $check_email = $conn->prepare("SELECT usersid FROM personal_information WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $check_email->store_result();

    if ($check_email->num_rows > 0) {
        die("Error: Email is already registered.");
    }
    $check_email->close();

    // Insert user data
    $stmt = $conn->prepare("INSERT INTO personal_information (email, password_hash, fullname, nickname, rank, confirmed, usertype, created_at) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    if (!$stmt) {
        die("SQL Error: " . $conn->error);
    }

    $stmt->bind_param("ssssssis", $email, $password_hash, $fullname, $nickname, $rank, $confirmed, $usertype, $created_at);

    if ($stmt->execute()) {
        echo "Success: Account created! Redirecting to login...";
        echo "<script>
                setTimeout(function() {
                    window.location.href = 'LoginAccount.php';
                }, 2000); // 2 seconds
              </script>";
        exit();
    }
    else {
        die("Error: " . $stmt->error);
    }

    // Close resources
    $stmt->close();
    $conn->close();
}
?>
