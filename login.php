
<?php
require_once "db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = strtolower(trim($_POST["email"] ?? ""));
    $password = $_POST["password"] ?? "";

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user["password"])) {
        session_regenerate_id(true);

        $_SESSION["user_id"] = $user["id"];
        $_SESSION["name"] = $user["name"];
        $_SESSION["role"] = $user["role"];

        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Incorrect email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login | SAKSHAM</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<nav class="navbar">
    <a class="logo" href="home.php">SAKSHAM</a>
    <div class="nav-links"><a href="home.php">Home</a><a href="register.php">Register</a></div>
</nav>

<div class="form-box">
    <h2>Welcome Back</h2>

    <?php if (isset($_GET["registered"])): ?>
        <div class="message">Account created! Please log in.</div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="message"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST">
        <label for="email">Email Address</label>
        <input id="email" type="email" name="email" required
               autocomplete="email">

        <label for="password">Password</label>
        <input id="password" type="password" name="password" required
               autocomplete="current-password">

        <button class="btn btn-blue" type="submit">Login</button>
    </form>

    <p>New to SAKSHAM? <a href="register.php">Create account</a></p>
</div>
</body>
</html>
