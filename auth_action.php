<?php
require_once __DIR__ . '/includes/auth.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'login') {
    $identifier = trim($_POST['identifier'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($identifier) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Please enter username/email and password.']);
        exit;
    }

    $stmt = mysqli_prepare($conn, "SELECT id, username, email, password, full_name, role FROM users WHERE username = ? OR email = ?");
    mysqli_stmt_bind_param($stmt, "ss", $identifier, $identifier);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($res);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        echo json_encode([
            'success' => true,
            'message' => 'Login successful. Welcome back, ' . htmlspecialchars($user['username']) . '!',
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role']
            ]
        ]);
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid username/email or password.']);
        exit;
    }
}

if ($action === 'register') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $fullName = trim($_POST['full_name'] ?? '');

    if (empty($username) || empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
        exit;
    }

    if (strlen($username) < 3) {
        echo json_encode(['success' => false, 'message' => 'Username must be at least 3 characters.']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Please provide a valid email address.']);
        exit;
    }

    if (strlen($password) < 6) {
        echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters.']);
        exit;
    }

    // Check existing
    $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ? OR email = ?");
    mysqli_stmt_bind_param($stmt, "ss", $username, $email);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if (mysqli_fetch_assoc($res)) {
        echo json_encode(['success' => false, 'message' => 'Username or email is already taken.']);
        exit;
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $insertStmt = mysqli_prepare($conn, "INSERT INTO users (username, email, password, full_name, role) VALUES (?, ?, ?, ?, 'customer')");
    mysqli_stmt_bind_param($insertStmt, "ssss", $username, $email, $hashed, $fullName);
    
    if (mysqli_stmt_execute($insertStmt)) {
        $newId = mysqli_insert_id($conn);
        $_SESSION['user_id'] = $newId;
        $_SESSION['username'] = $username;
        $_SESSION['role'] = 'customer';

        echo json_encode([
            'success' => true,
            'message' => 'Account created successfully! You are now logged in.',
            'user' => ['id' => $newId, 'username' => $username, 'role' => 'customer']
        ]);
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error during registration.']);
        exit;
    }
}

if ($action === 'logout') {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();

    if (isset($_GET['redirect'])) {
        header("Location: " . $_GET['redirect']);
        exit;
    }

    echo json_encode(['success' => true, 'message' => 'Logged out successfully.']);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid action.']);
exit;
?>
