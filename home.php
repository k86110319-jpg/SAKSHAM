
<?php
require_once "db.php";
$totalCourses = $pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();
$totalTeachers = $pdo->query("SELECT COUNT(*) FROM users WHERE role='teacher'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SAKSHAM | Inclusive Education</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="accessibility-toolbar">
    <button onclick="changeFontSize(2)">A+</button>
    <button onclick="changeFontSize(-2)">A-</button>
    <button onclick="toggleContrast()">High Contrast</button>
    <button onclick="readPage()">🔊 Read Aloud</button>
    <button onclick="stopReading()">Stop</button>
</div>

<link rel="stylesheet" href="accessibility.css">
<script src="accessibility.js"></script>

<nav class="navbar">
    <a class="logo" href="home.php">SAKSHAM</a>
    <div class="nav-links">
        <a href="home.php">Home</a>
        <a href="courses.php">Explore Courses</a>
        <a href="register.php">Register</a>
        <a href="login.php">Login</a>
    </div>
</nav>

<section class="hero">
    <h1>Learn Without Barriers. Teach Without Limits.</h1>
    <p>
        Affordable and accessible education for every learner.
        Discover courses, study materials and inclusive learning
        opportunities in one place.
    </p>
    <a class="btn" href="courses.php">Explore Courses</a>
    <a class="btn btn-blue" href="register.php">Join SAKSHAM</a>
</section>

<main class="container">
    <h2 class="section-title">Education for Everyone</h2>

    <div class="cards">
        <div class="card">
            <h3>For Students</h3>
            <p>Discover affordable courses, learn at your own pace
            and access useful study resources.</p>
        </div>

        <div class="card">
            <h3>For Teachers</h3>
            <p>Share your knowledge, publish courses and reach
            learners through an inclusive platform.</p>
        </div>

        <div class="card">
            <h3>For Parents</h3>
            <p>Support your child's education and explore
            accessible learning resources.</p>
        </div>

        <div class="card">
            <h3>Accessible Learning</h3>
            <p>Use readable layouts, keyboard-friendly controls
            and learning content designed with accessibility in mind.</p>
        </div>
    </div>

    <h2 class="section-title" style="margin-top:45px">
        Our Learning Community
    </h2>

    <div class="cards">
        <div class="card">
            <h3><?php echo (int)$totalCourses; ?>+</h3>
            <p>Courses listed</p>
        </div>
        <div class="card">
            <h3><?php echo (int)$totalTeachers; ?>+</h3>
            <p>Registered teachers</p>
        </div>
        <div class="card">
            <h3>Affordable</h3>
            <p>Free and budget-friendly learning options</p>
        </div>
    </div>
</main>

<footer>
    <p>© <?php echo date("Y"); ?> SAKSHAM. Inclusive Education for Everyone.</p>
</footer>

</body>
</html>
