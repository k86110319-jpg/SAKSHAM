
<?php
require_once "db.php";

$stmt = $pdo->query(
    "SELECT c.*,u.name AS teacher_name
     FROM courses c JOIN users u ON c.teacher_id=u.id
     ORDER BY c.created_at DESC"
);
$courses = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Explore Courses | SAKSHAM</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<nav class="navbar">
    <a class="logo" href="home.php">SAKSHAM</a>
    <div class="nav-links">
        <a href="home.php">Home</a>
        <?php if (isset($_SESSION["user_id"])): ?>
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </div>
</nav>

<main class="container">
    <h1 class="section-title">Explore Affordable Courses</h1>

    <input type="search" id="courseSearch"
           placeholder="Search by course or subject..."
           aria-label="Search courses">

    <div class="cards" id="courseList">
        <?php foreach ($courses as $course): ?>
            <article class="card course-card">
                <h3><?php echo htmlspecialchars($course["title"]); ?></h3>
                <p><strong>Category:</strong>
                    <?php echo htmlspecialchars($course["category"]); ?></p>
                <p><?php echo nl2br(htmlspecialchars($course["description"])); ?></p>
                <p>Instructor: <?php echo htmlspecialchars($course["teacher_name"]); ?></p>
                <p class="course-price">
                    <?php echo ((float)$course["price"] <= 0)
                        ? "Free"
                        : "₹" . number_format((float)$course["price"], 2); ?>
                </p>

                <?php if (isset($_SESSION["user_id"]) && $_SESSION["role"] === "student"): ?>
                    <form method="POST" action="enroll.php">
                        <input type="hidden" name="course_id"
                               value="<?php echo (int)$course["id"]; ?>">
                        <button class="btn btn-blue" type="submit">Enrol Now</button>
                    </form>
                <?php elseif (!isset($_SESSION["user_id"])): ?>
                    <a class="btn btn-blue" href="login.php">Login to Enrol</a>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
    </div>

    <?php if (!$courses): ?>
        <div class="card">
            <h3>No courses yet</h3>
            <p>Teacher-created courses will appear here.</p>
        </div>
    <?php endif; ?>
</main>

<script>
const search = document.getElementById("courseSearch");
search.addEventListener("input", function () {
    const value = this.value.toLowerCase();
    document.querySelectorAll(".course-card").forEach(card => {
        card.style.display = card.innerText.toLowerCase().includes(value)
            ? "" : "none";
    });
});
</script>
</body>
</html>
