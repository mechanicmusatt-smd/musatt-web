<?php
session_start();

// Hardcoded credentials for demo
$validUsers = [
    "user1" => "password123",
    "admin" => "adminpass"
];

$callbackUrl = "https://example.com/verify"; // Replace with your target verification URL
$loginMessage = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Step 1: Validate locally
    if (isset($validUsers[$username]) && $validUsers[$username] === $password) {

        // Step 2: Send data to callback URL for verification
        $data = json_encode([
            "username" => $username,
            "timestamp" => time()
        ]);

        $ch = curl_init($callbackUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json"
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);

        // Step 3: Check verification response
        if (isset($result['status']) && $result['status'] === "approved") {
            $_SESSION['user'] = $username;
            header("Location: dashboard.php");
            exit;
        } else {
            $loginMessage = "Verification failed.";
        }

    } else {
        $loginMessage = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login Page</title>
    <style>
        body { font-family: Arial; background: #f0f0f0; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .login-box { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px #aaa; width: 300px; }
        input[type=text], input[type=password] { width: 100%; padding: 10px; margin: 5px 0; border-radius: 4px; border: 1px solid #ccc; }
        input[type=submit] { width: 100%; padding: 10px; border: none; border-radius: 4px; background: #28a745; color: #fff; cursor: pointer; }
        input[type=submit]:hover { background: #218838; }
        .error { color: red; margin: 10px 0; }
    </style>
</head>
<body>
<div class="login-box">
    <h2>Login</h2>
    <?php if ($loginMessage): ?>
        <div class="error"><?= htmlspecialchars($loginMessage) ?></div>
    <?php endif; ?>
    <form method="post">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="submit" value="Login">
    </form>
</div>
</body>
</html>


