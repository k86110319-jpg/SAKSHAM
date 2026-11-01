
<?php
require_once "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (($_SESSION['role'] ?? '') !== 'teacher') {
    exit("Only teachers can publish courses.");
}

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = trim($_POST['title'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = $_POST['price'] ?? '';

    if ($title === '' || $category === '' || $description === '' || $price === '') {
        $error = "Please fill in all fields.";
    } elseif (!is_numeric($price) || (float)$price < 0) {
        $error = "Please enter a valid course price.";
    } else {
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO courses
                (teacher_id, title, category, description, price)
                VALUES (?, ?, ?, ?, ?)"
            );

            $stmt->execute([
                $_SESSION['user_id'],
                $title,
                $category,
                $description,
                $price
            ]);

            $message = "Your course has been published successfully!";

        } catch (PDOException $e) {
            $error = "Unable to publish the course. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publish Course | SAKSHAM</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f1f5fb;
            margin: 0;
            padding: 25px;
        }

        .container {
            max-width: 650px;
            margin: 30px auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 15px #0001;
        }

        h1 {
            color: #174ea6;
        }

        label {
            display: block;
            margin-top: 18px;
            font-weight: bold;
        }

        input, select, textarea {
            width: 100%;
            padding: 12px;
            margin-top: 8px;
            border: 1px solid #ccc;
            border-radius: 7px;
            box-sizing: border-box;
            font-size: 15px;
        }

        textarea {
            min-height: 130px;
            resize: vertical;
        }

        button {
            margin-top: 22px;
            padding: 13px 20px;
            background: #174ea6;
            color: white;
            border: none;
            border-radius: 7px;
            cursor: pointer;
            font-size: 16px;
        }

        .success {
            background: #e1f7e9;
            color: #176b37;
            padding: 12px;
            border-radius: 7px;
        }

        .error {
            background: #ffe5e5;
            color: #a00000;
            padding: 12px;
            border-radius: 7px;
        }

        a {
            color: #174ea6;
        }
    </style>
</head>

<body>

<div class="container">

    <h1>📚 Publish a New Course</h1>
    <p>Share your knowledge with SAKSHAM students.</p>

    <?php if ($message): ?>
        <p class="success">
            <?php echo htmlspecialchars($message); ?>
        </p>
    <?php endif; ?>

    <?php if ($error): ?>
        <p class="error">
            <?php echo htmlspecialchars($error); ?>
        </p>
    <?php endif; ?>

    <form method="POST">

        <label>Course Title</label>
        <input type="text" name="title"
               placeholder="Example: Web Development"
               maxlength="200" required>

        <label>Course Category</label>
        <select name="category" required>
            <option value="">Select Category</option>
            <option>Programming</option>
            <option>Web Development</option>
            <option>Computer Science</option>
            <option>Database</option>
            <option>Communication</option>
            <option>Mathematics</option>
            <option>Other</option>
        </select>

        <label>Course Description</label>
        <textarea name="description"
                  placeholder="Explain what students will learn..."
                  required></textarea>

        <label>Course Price (₹)</label>
        <input type="number" name="price"
               min="0" step="0.01"
               placeholder="Example: 199"
               required>

        <button type="submit">Publish Course</button>

    </form>

    <br>

    <a href="dashboard.php">← Back to Dashboard</a>
    <br><br>
    <a href="courses.php">View All Courses</a>

</div>

</body>
</html>
