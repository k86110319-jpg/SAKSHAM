
<?php
require_once "db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = strtolower(trim($_POST["email"] ?? ""));
    $password = $_POST["password"] ?? "";
    $role = $_POST["role"] ?? "";

    if ($name === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)
        || strlen($password) < 8
        || !in_array($role, ["student", "teacher", "parent"], true)) {
        $error = "Enter valid details. Password must be at least 8 characters.";
    } else {
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);

        if ($check->fetch()) {
            $error = "This email is already registered.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare(
                "INSERT INTO users (name,email,password,role) VALUES (?,?,?,?)"
            );
            $stmt->execute([$name, $email, $hash, $role]);

            header("Location: login.php?registered=1");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register | SAKSHAM</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<nav class="navbar">
    <a class="logo" href="home.php">SAKSHAM</a>
    <div class="nav-links"><a href="home.php">Home</a><a href="login.php">Login</a></div>
</nav>

<div class="form-box">
    <h2>Create Your Account</h2>
    <p>Join the SAKSHAM learning community.</p>

    <?php if ($error): ?>
        <div class="message"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST">
        <label for="name">Full Name</label>
        <input id="name" name="name" required maxlength="100"
               autocomplete="name">

        <label for="email">Email Address</label>
        <input id="email" type="email" name="email" required
               autocomplete="email">

        <label for="password">Password (minimum 8 characters)</label>
        <input id="password" type="password" name="password"
               minlength="8" required autocomplete="new-password">

        <label for="role">Register As</label>
        <select id="role" name="role" required>
            <option value="">Choose your role</option>
            <option value="student">Student</option>
            <option value="teacher">Teacher / Instructor</option>
            <option value="parent">Parent</option>
        </select>

        <button class="btn btn-blue" type="submit">Create Account</button>
    </form>

    <p>Already registered? <a href="login.php">Login here</a></p>
</div>
</body>
</html>
