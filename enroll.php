
<?php
require_once "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION["role"] !== "student") {
    http_response_code(403);
    exit("Only students can enrol in courses.");
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: courses.php");
    exit;
}

$courseId = filter_input(INPUT_POST, "course_id", FILTER_VALIDATE_INT);

if (!$courseId) {
    exit("Invalid course.");
}

$stmt = $pdo->prepare("SELECT id FROM courses WHERE id=?");
$stmt->execute([$courseId]);

if (!$stmt->fetch()) {
    exit("Course not found.");
}

try {
    $stmt = $pdo->prepare(
        "INSERT INTO enrollments (student_id,course_id) VALUES (?,?)"
    );
    $stmt->execute([(int)$_SESSION["user_id"], $courseId]);
    $message = "You have successfully enrolled!";
} catch (PDOException $e) {
    $message = "You are already enrolled in this course.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Course Enrolment | SAKSHAM</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="form-box">
    <h2>Course Enrolment</h2>
    <p><?php echo htmlspecialchars($message); ?></p>
    <a class="btn btn-blue" href="dashboard.php">Go to Dashboard</a>
    <a class="btn" href="courses.php">Explore More Courses</a>
</div>
</body>
</html>
