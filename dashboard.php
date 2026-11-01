
<?php
require_once "db.php";

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'] ?? 'User';
$role = $_SESSION['role'] ?? 'student';

// Student statistics
$enrolled_count = 0;
$completed_count = 0;
$available_courses = 0;

// Teacher statistics
$teacher_courses = 0;
$total_students = 0;

try {
    if ($role === 'student') {

        $stmt = $pdo->prepare(
            "SELECT COUNT(*) FROM enrollments WHERE student_id = ?"
        );
        $stmt->execute([$user_id]);
        $enrolled_count = $stmt->fetchColumn();

        $stmt = $pdo->prepare(
            "SELECT COUNT(*) FROM lesson_progress
             WHERE student_id = ?
             AND total_lessons > 0
             AND completed_lessons >= total_lessons"
        );
        $stmt->execute([$user_id]);
        $completed_count = $stmt->fetchColumn();

        $available_courses = $pdo->query(
            "SELECT COUNT(*) FROM courses"
        )->fetchColumn();
    }

    if ($role === 'teacher') {

        $stmt = $pdo->prepare(
            "SELECT COUNT(*) FROM courses WHERE teacher_id = ?"
        );
        $stmt->execute([$user_id]);
        $teacher_courses = $stmt->fetchColumn();

        $stmt = $pdo->prepare(
            "SELECT COUNT(DISTINCT e.student_id)
             FROM enrollments e
             JOIN courses c ON e.course_id = c.id
             WHERE c.teacher_id = ?"
        );
        $stmt->execute([$user_id]);
        $total_students = $stmt->fetchColumn();
    }

} catch (PDOException $e) {
    // Keep dashboard available if a statistics query fails.
}

// Latest courses
$latest_courses = [];

try {
    $stmt = $pdo->query(
        "SELECT courses.*, users.name AS teacher_name
         FROM courses
         JOIN users ON courses.teacher_id = users.id
         ORDER BY courses.created_at DESC
         LIMIT 6"
    );

    $latest_courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $latest_courses = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>SAKSHAM | Dashboard</title>

    <link rel="stylesheet" href="accessibility.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f4f7fc;
            color: #17233c;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        .navbar {
            background: #153f83;
            color: white;
            padding: 18px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .logo {
            font-size: 25px;
            font-weight: bold;
        }

        .nav-links {
            display: flex;
            gap: 18px;
            align-items: center;
            flex-wrap: wrap;
        }

        .nav-links a {
            color: white;
            font-size: 14px;
        }

        .logout {
            background: #ffcf55;
            color: #17233c !important;
            padding: 9px 15px;
            border-radius: 7px;
            font-weight: bold;
        }

        .container {
            max-width: 1200px;
            margin: auto;
            padding: 25px 20px;
        }

        .welcome {
            background: linear-gradient(120deg, #174ea6, #4285d4);
            color: white;
            padding: 35px;
            border-radius: 16px;
            margin-bottom: 25px;
        }

        .welcome h1 {
            font-size: 29px;
            margin-bottom: 10px;
        }

        .welcome p {
            line-height: 1.6;
        }

        .accessibility-toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: 9px;
            margin-bottom: 25px;
            padding: 15px;
            background: white;
            border-radius: 12px;
        }

        .accessibility-toolbar button {
            background: #153f83;
            color: white;
            border: none;
            padding: 10px 14px;
            border-radius: 7px;
            cursor: pointer;
        }

        .section-title {
            margin: 28px 0 16px;
            font-size: 22px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
            gap: 18px;
        }

        .stat-card {
            background: white;
            padding: 22px;
            border-radius: 13px;
            box-shadow: 0 3px 12px #0000000a;
        }

        .stat-card h2 {
            color: #174ea6;
            font-size: 30px;
            margin-bottom: 8px;
        }

        .stat-card p {
            color: #65728a;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 18px;
        }

        .feature-card {
            background: white;
            padding: 23px;
            border-radius: 13px;
            border: 1px solid #e3eaf5;
            transition: 0.2s;
        }

        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 6px 18px #00000012;
        }

        .feature-card .emoji {
            font-size: 30px;
            margin-bottom: 12px;
        }

        .feature-card h3 {
            margin-bottom: 9px;
            color: #174ea6;
        }

        .feature-card p {
            color: #667085;
            font-size: 14px;
            line-height: 1.6;
        }

        .course-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 18px;
        }

        .course-card {
            background: white;
            border-radius: 13px;
            padding: 20px;
            border: 1px solid #e3eaf5;
        }

        .course-card h3 {
            color: #174ea6;
            margin-bottom: 10px;
        }

        .course-card p {
            margin-bottom: 9px;
            color: #65728a;
            font-size: 14px;
        }

        .course-price {
            font-weight: bold;
            color: #16804a !important;
        }

        .primary-btn {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 15px;
            background: #174ea6;
            color: white;
            border-radius: 7px;
        }

        .empty-message {
            background: white;
            padding: 20px;
            border-radius: 10px;
            color: #65728a;
        }

        footer {
            text-align: center;
            padding: 25px;
            margin-top: 35px;
            background: #153f83;
            color: white;
        }

        button:focus, a:focus {
            outline: 3px solid orange;
            outline-offset: 3px;
        }

        @media(max-width: 600px) {
            .navbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .welcome {
                padding: 25px;
            }

            .welcome h1 {
                font-size: 23px;
            }
        }
    </style>
</head>

<body>

<!-- NAVIGATION -->
<nav class="navbar">

    <div class="logo">SAKSHAM ♿</div>

    <div class="nav-links">
        <a href="home.php">Home</a>
        <a href="courses.php">Courses</a>
        <a href="study-tools.php">Study Tools</a>
        <a href="logout.php" class="logout">Logout</a>
    </div>

</nav>

<main class="container">

    <!-- WELCOME SECTION -->
    <section class="welcome">

        <h1>Welcome, <?php echo htmlspecialchars($user_name); ?>! 👋</h1>

        <p>
            Welcome to SAKSHAM – Inclusive Learning for Everyone.
            Learn without barriers and teach without limits.
        </p>

        <p style="margin-top:12px;">
            Account Type:
            <strong><?php echo htmlspecialchars(ucfirst($role)); ?></strong>
        </p>

    </section>

    <!-- ACCESSIBILITY TOOLBAR -->
    <section class="accessibility-toolbar"
             aria-label="Accessibility controls">

        <button onclick="changeFontSize(2)">A+</button>
        <button onclick="changeFontSize(-2)">A-</button>
        <button onclick="toggleContrast()">High Contrast</button>
        <button onclick="readPage()">🔊 Read Aloud</button>
        <button onclick="stopReading()">Stop</button>

    </section>

    <?php if ($role === 'student'): ?>

        <!-- STUDENT STATISTICS -->
        <h2 class="section-title">Your Learning Overview</h2>

        <section class="stats-grid">

            <div class="stat-card">
                <h2><?php echo (int)$enrolled_count; ?></h2>
                <p>Enrolled Courses</p>
            </div>

            <div class="stat-card">
                <h2><?php echo (int)$completed_count; ?></h2>
                <p>Completed Courses</p>
            </div>

            <div class="stat-card">
                <h2><?php echo (int)$available_courses; ?></h2>
                <p>Available Courses</p>
            </div>

        </section>

        <!-- STUDENT FEATURES -->
        <h2 class="section-title">Student Learning Zone</h2>

        <section class="feature-grid">

           <a href="courses.php" class="feature-card">
    <div class="emoji">📚</div>
    <h3>Explore All Courses</h3>
    <p>Discover affordable courses and learn new skills.</p>
</a>

            <a href="study-tools.php" class="feature-card">
                <div class="emoji">🧠</div>
                <h3>Smart Study Tools</h3>
                <p>Summarize notes and generate practice quizzes.</p>
            </a>

            <a href="courses.php" class="feature-card">
                <div class="emoji">🎓</div>
                <h3>My Learning</h3>
                <p>Explore courses and access your learning opportunities.</p>
            </a>

        </section>

    <?php elseif ($role === 'teacher'): ?>

        <!-- TEACHER STATISTICS -->
        <h2 class="section-title">Your Teaching Overview</h2>

        <section class="stats-grid">

            <div class="stat-card">
                <h2><?php echo (int)$teacher_courses; ?></h2>
                <p>Published Courses</p>
            </div>

            <div class="stat-card">
                <h2><?php echo (int)$total_students; ?></h2>
                <p>Enrolled Students</p>
            </div>

        </section>

        <!-- TEACHER FEATURES -->
        <h2 class="section-title">Teacher Workspace</h2>

        <section class="feature-grid">

            <a href="add-course.php" class="feature-card">
                <div class="emoji">➕</div>
                <h3>Publish a Course</h3>
                <p>Create a course and share your knowledge with students.</p>
            </a>

            <a href="courses.php" class="feature-card">
                <div class="emoji">📖</div>
                <h3>View Courses</h3>
                <p>Explore courses available on the platform.</p>
            </a>

            <a href="study-tools.php" class="feature-card">
                <div class="emoji">🧠</div>
                <h3>Study Tools</h3>
                <p>Try the study assistant and quiz generator.</p>
            </a>

        </section>

    <?php elseif ($role === 'parent'): ?>

        <!-- PARENT FEATURES -->
        <h2 class="section-title">Parent Dashboard</h2>

        <section class="feature-grid">

            <a href="courses.php" class="feature-card">
                <div class="emoji">📚</div>
                <h3>Explore Learning</h3>
                <p>Browse affordable learning opportunities for students.</p>
            </a>

            <div class="feature-card">
                <div class="emoji">📊</div>
                <h3>Learning Progress</h3>
                <p>Student linking and detailed progress reports are coming soon.</p>
            </div>

            <div class="feature-card">
                <div class="emoji">🔔</div>
                <h3>Learning Updates</h3>
                <p>Stay connected with your child's learning journey.</p>
            </div>

        </section>

    <?php else: ?>

        <div class="empty-message">
            Your account role is not supported by this dashboard.
            Please contact the administrator.
        </div>

    <?php endif; ?>


    <!-- LATEST COURSES -->
    <h2 class="section-title">Recently Added Courses</h2>

    <?php if (count($latest_courses) > 0): ?>

        <section class="course-grid">

            <?php foreach ($latest_courses as $course): ?>

                <article class="course-card">

                    <h3>
                        <?php echo htmlspecialchars($course['title']); ?>
                    </h3>

                    <p>
                        Subject:
                        <?php echo htmlspecialchars($course['category']); ?>
                    </p>

                    <p>
                        Teacher:
                        <?php echo htmlspecialchars($course['teacher_name']); ?>
                    </p>

                    <p class="course-price">
                        Price: ₹<?php echo htmlspecialchars($course['price']); ?>
                    </p>

                    <a href="courses.php" class="primary-btn">
                        Explore Course
                    </a>

                </article>

            <?php endforeach; ?>

        </section>

    <?php else: ?>

        <div class="empty-message">
            No courses have been published yet.
            Check back soon!
        </div>

    <?php endif; ?>

</main>

<footer>
    <p>© <?php echo date("Y"); ?> SAKSHAM – Learn Without Barriers. Teach Without Limits.</p>
</footer>

<script src="accessibility.js"></script>

</body>
</html>
