<?php
session_start();

$dbPath = __DIR__ . '/database.sqlite';

try {
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec('PRAGMA foreign_keys = ON;');

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            username TEXT NOT NULL UNIQUE,
            email TEXT NOT NULL UNIQUE,
            password TEXT NOT NULL,
            role TEXT NOT NULL CHECK(role IN ('admin', 'student')),
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS courses (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            code TEXT NOT NULL UNIQUE,
            name TEXT NOT NULL,
            credits INTEGER NOT NULL,
            description TEXT
        )
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS students (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL UNIQUE,
            course_id INTEGER,
            phone TEXT,
            gender TEXT,
            date_of_birth TEXT,
            address TEXT,
            enrollment_date TEXT,
            status TEXT DEFAULT 'active',
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE SET NULL
        )
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS marks (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            student_id INTEGER NOT NULL,
            course_id INTEGER NOT NULL,
            exam_name TEXT NOT NULL,
            mark INTEGER NOT NULL,
            total INTEGER NOT NULL,
            score_percent REAL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            UNIQUE(student_id, course_id, exam_name),
            FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
            FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
        )
    ");

    $userCount = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
    if ($userCount === 0) {
        $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $studentPassword = password_hash('student123', PASSWORD_DEFAULT);

        $pdo->exec("INSERT INTO users (name, username, email, password, role) VALUES
            ('System Admin', 'admin', 'admin@school.com', '$adminPassword', 'admin'),
            ('Demo Student', 'student', 'student@school.com', '$studentPassword', 'student')");

        $adminId = (int) $pdo->lastInsertId();
        $studentUserId = (int) $pdo->lastInsertId();

        $pdo->exec("INSERT INTO courses (code, name, credits, description) VALUES
            ('CS101', 'Computer Science Fundamentals', 3, 'Core programming and computer science basics'),
            ('MATH201', 'Mathematics II', 4, 'Advanced mathematics concepts and applications'),
            ('ENG205', 'English Communication', 2, 'Writing and speaking skills')");

        $courseRow = $pdo->query("SELECT id FROM courses ORDER BY id ASC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        $courseId = $courseRow ? (int) $courseRow['id'] : 1;

        $pdo->exec("INSERT INTO students (user_id, course_id, phone, gender, date_of_birth, address, enrollment_date, status) VALUES
            ($studentUserId, $courseId, '555-0101', 'Female', '2003-05-14', '123 Campus Road', '2024-01-15', 'active')");

        $studentId = (int) $pdo->lastInsertId();
        $pdo->exec("INSERT INTO marks (student_id, course_id, exam_name, mark, total, score_percent) VALUES
            ($studentId, $courseId, 'Midterm', 78, 100, 78.0),
            ($studentId, $courseId, 'Final', 85, 100, 85.0)");
    }

    $courseCount = (int) $pdo->query('SELECT COUNT(*) FROM courses')->fetchColumn();
    if ($courseCount === 0) {
        $pdo->exec("INSERT INTO courses (code, name, credits, description) VALUES
            ('CS101', 'Computer Science Fundamentals', 3, 'Core programming and computer science basics'),
            ('MATH201', 'Mathematics II', 4, 'Advanced mathematics concepts and applications'),
            ('ENG205', 'English Communication', 2, 'Writing and speaking skills')");
    }
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function requireLogin(array $allowedRoles = ['admin', 'student']): void
{
    if (empty($_SESSION['user_id'])) {
        redirect('login.php');
    }

    if (!in_array($_SESSION['role'], $allowedRoles, true)) {
        redirect('dashboard.php');
    }
}

function getUserByUsername(PDO $pdo, string $username): ?array
{
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = :username LIMIT 1');
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch();
    return $user ?: null;
}

function getStudentByUserId(PDO $pdo, int $userId): ?array
{
    $stmt = $pdo->prepare("
        SELECT s.*, u.name, u.email, c.name AS course_name, c.code AS course_code
        FROM students s
        LEFT JOIN users u ON u.id = s.user_id
        LEFT JOIN courses c ON c.id = s.course_id
        WHERE s.user_id = :user_id LIMIT 1
    ");
    $stmt->execute([':user_id' => $userId]);
    return $stmt->fetch() ?: null;
}

function getStudentById(PDO $pdo, int $studentId): ?array
{
    $stmt = $pdo->prepare("
        SELECT s.*, u.name, u.email, u.username, c.name AS course_name, c.code AS course_code
        FROM students s
        LEFT JOIN users u ON u.id = s.user_id
        LEFT JOIN courses c ON c.id = s.course_id
        WHERE s.id = :id LIMIT 1
    ");
    $stmt->execute([':id' => $studentId]);
    return $stmt->fetch() ?: null;
}

function getAllStudents(PDO $pdo): array
{
    $stmt = $pdo->query("
        SELECT s.*, u.name, u.email, c.name AS course_name, c.code AS course_code
        FROM students s
        JOIN users u ON u.id = s.user_id
        LEFT JOIN courses c ON c.id = s.course_id
        ORDER BY s.id DESC
    ");
    return $stmt->fetchAll();
}

function getAllCourses(PDO $pdo): array
{
    $stmt = $pdo->query('SELECT * FROM courses ORDER BY id DESC');
    return $stmt->fetchAll();
}

function getCourseById(PDO $pdo, int $courseId): ?array
{
    $stmt = $pdo->prepare('SELECT * FROM courses WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $courseId]);
    $course = $stmt->fetch();
    return $course ?: null;
}

function getMarksForStudent(PDO $pdo, int $studentId): array
{
    $stmt = $pdo->prepare("
        SELECT m.*, c.name AS course_name, c.code AS course_code
        FROM marks m
        JOIN courses c ON c.id = m.course_id
        WHERE m.student_id = :student_id
        ORDER BY m.created_at DESC
    ");
    $stmt->execute([':student_id' => $studentId]);
    return $stmt->fetchAll();
}

function getStudentIdByUserId(PDO $pdo, int $userId): ?int
{
    $stmt = $pdo->prepare('SELECT id FROM students WHERE user_id = :user_id LIMIT 1');
    $stmt->execute([':user_id' => $userId]);
    $row = $stmt->fetch();
    return $row ? (int) $row['id'] : null;
}

function calculateAverageMark(PDO $pdo, int $studentId): float
{
    $stmt = $pdo->prepare('SELECT AVG(score_percent) AS avg FROM marks WHERE student_id = :student_id');
    $stmt->execute([':student_id' => $studentId]);
    $row = $stmt->fetch();
    return (float) ($row['avg'] ?? 0.0);
}

function formatDate(?string $date): string
{
    if (empty($date)) {
        return 'N/A';
    }

    return date('d M, Y', strtotime($date));
}

function isAdmin(): bool
{
    return ($_SESSION['role'] ?? '') === 'admin';
}

function isStudent(): bool
{
    return ($_SESSION['role'] ?? '') === 'student';
}

function getCurrentUser(PDO $pdo): ?array
{
    if (empty($_SESSION['user_id'])) {
        return null;
    }

    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $_SESSION['user_id']]);
    return $stmt->fetch() ?: null;
}

function createStudentRecord(PDO $pdo, array $data): void
{
    $userStmt = $pdo->prepare('INSERT INTO users (name, username, email, password, role) VALUES (:name, :username, :email, :password, :role)');
    $userStmt->execute([
        ':name' => $data['name'],
        ':username' => $data['username'],
        ':email' => $data['email'],
        ':password' => password_hash($data['password'], PASSWORD_DEFAULT),
        ':role' => 'student',
    ]);

    $userId = (int) $pdo->lastInsertId();
    $studentStmt = $pdo->prepare('INSERT INTO students (user_id, course_id, phone, gender, date_of_birth, address, enrollment_date, status) VALUES (:user_id, :course_id, :phone, :gender, :date_of_birth, :address, :enrollment_date, :status)');
    $studentStmt->execute([
        ':user_id' => $userId,
        ':course_id' => $data['course_id'] ?? null,
        ':phone' => $data['phone'] ?? null,
        ':gender' => $data['gender'] ?? null,
        ':date_of_birth' => $data['date_of_birth'] ?? null,
        ':address' => $data['address'] ?? null,
        ':enrollment_date' => $data['enrollment_date'] ?? date('Y-m-d'),
        ':status' => $data['status'] ?? 'active',
    ]);
}

function deleteStudent(PDO $pdo, int $studentId): void
{
    $student = getStudentById($pdo, $studentId);
    if (!$student) {
        return;
    }

    $pdo->prepare('DELETE FROM users WHERE id = :user_id')->execute([':user_id' => $student['user_id']]);
}

function getDashboardStats(PDO $pdo): array
{
    $studentCount = (int) $pdo->query('SELECT COUNT(*) FROM students')->fetchColumn();
    $courseCount = (int) $pdo->query('SELECT COUNT(*) FROM courses')->fetchColumn();
    $userCount = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
    $avgMark = (float) $pdo->query('SELECT AVG(score_percent) FROM marks')->fetchColumn();

    return [
        'students' => $studentCount,
        'courses' => $courseCount,
        'users' => $userCount,
        'avg_mark' => $avgMark,
    ];
}

function getRecentStudents(PDO $pdo): array
{
    $stmt = $pdo->query("
        SELECT s.*, u.name, c.name AS course_name
        FROM students s
        JOIN users u ON u.id = s.user_id
        LEFT JOIN courses c ON c.id = s.course_id
        ORDER BY s.id DESC
        LIMIT 5
    ");
    return $stmt->fetchAll();
}

function getStudentCourseMarks(PDO $pdo, int $studentId): array
{
    $stmt = $pdo->prepare("
        SELECT m.*, c.name AS course_name, c.code AS course_code
        FROM marks m
        JOIN courses c ON c.id = m.course_id
        WHERE m.student_id = :student_id
        ORDER BY c.name ASC, m.exam_name ASC
    ");
    $stmt->execute([':student_id' => $studentId]);
    return $stmt->fetchAll();
}

function getAllMarkEntries(PDO $pdo): array
{
    $stmt = $pdo->query("
        SELECT m.*, s.id AS student_id, u.name AS student_name, c.name AS course_name, c.code AS course_code
        FROM marks m
        JOIN students s ON s.id = m.student_id
        JOIN users u ON u.id = s.user_id
        JOIN courses c ON c.id = m.course_id
        ORDER BY m.created_at DESC
    ");
    return $stmt->fetchAll();
}

function updateStudentRecord(PDO $pdo, int $studentId, array $data): void
{
    $stmt = $pdo->prepare("
        UPDATE students
        SET course_id = :course_id,
            phone = :phone,
            gender = :gender,
            date_of_birth = :date_of_birth,
            address = :address,
            enrollment_date = :enrollment_date,
            status = :status
        WHERE id = :id
    ");
    $stmt->execute([
        ':course_id' => $data['course_id'] ?? null,
        ':phone' => $data['phone'] ?? null,
        ':gender' => $data['gender'] ?? null,
        ':date_of_birth' => $data['date_of_birth'] ?? null,
        ':address' => $data['address'] ?? null,
        ':enrollment_date' => $data['enrollment_date'] ?? date('Y-m-d'),
        ':status' => $data['status'] ?? 'active',
        ':id' => $studentId,
    ]);

    $userStmt = $pdo->prepare("UPDATE users SET name = :name, email = :email WHERE id = :user_id");
    $userStmt->execute([
        ':name' => $data['name'],
        ':email' => $data['email'],
        ':user_id' => $data['user_id'],
    ]);
}

function saveMark(PDO $pdo, array $data): void
{
    $scorePercent = ($data['mark'] / $data['total']) * 100;
    $stmt = $pdo->prepare("
        INSERT INTO marks (student_id, course_id, exam_name, mark, total, score_percent)
        VALUES (:student_id, :course_id, :exam_name, :mark, :total, :score_percent)
        ON CONFLICT(student_id, course_id, exam_name)
        DO UPDATE SET mark = excluded.mark, total = excluded.total, score_percent = excluded.score_percent, created_at = CURRENT_TIMESTAMP
    ");
    $stmt->execute([
        ':student_id' => $data['student_id'],
        ':course_id' => $data['course_id'],
        ':exam_name' => $data['exam_name'],
        ':mark' => $data['mark'],
        ':total' => $data['total'],
        ':score_percent' => $scorePercent,
    ]);
}

function getMarksSummary(PDO $pdo, int $studentId): array
{
    $stmt = $pdo->prepare('SELECT COUNT(*) AS count, AVG(score_percent) AS avg, MAX(score_percent) AS max_score, MIN(score_percent) AS min_score FROM marks WHERE student_id = :student_id');
    $stmt->execute([':student_id' => $studentId]);
    return $stmt->fetch();
}

function enforceRole(string $role): void
{
    if (($_SESSION['role'] ?? '') !== $role) {
        redirect('dashboard.php');
    }
}

function currentUserName(): string
{
    return $_SESSION['name'] ?? 'User';
}

function pageHeader(string $title): string
{
    return "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>$title</title>
            <link rel='preconnect' href='https://fonts.googleapis.com'>
            <link rel='preconnect' href='https://fonts.gstatic.com' crossorigin>
            <link href='https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap' rel='stylesheet'>
            <link rel='stylesheet' href='styles.css'>
        </head>
        <body>
    ";
}

function pageFooter(): string
{
    return "
            <script src='script.js'></script>
        </body>
        </html>
    ";
}

function renderLayout(string $title, string $content): void
{
    echo pageHeader($title) . $content . pageFooter();
}

function navLinks(): string
{
    $role = $_SESSION['role'] ?? 'guest';
    $nav = "
    <header class='topbar'>
        <div class='brand'>
            <span class='brand-mark'>S</span>
            <span>StudentHub</span>
        </div>
        <nav class='main-nav'>
            <a href='dashboard.php'>Dashboard</a>
            <a href='students.php'>Students</a>
            <a href='courses.php'>Courses</a>
            <a href='marks.php'>Marks</a>
            " . ($role === 'admin' ? "<a href='students.php?new=1'>Add Student</a>" : '') . "
        </nav>
        <div class='user-menu'>
            <button id='themeToggle' class='theme-toggle' aria-label='Toggle theme'>☀️</button>
            <div class='user-pill'>" . htmlspecialchars(currentUserName()) . "</div>
            <a href='logout.php' class='btn btn-danger btn-small'>Logout</a>
        </div>
    </header>
    ";

    return $nav;
}

function alertBox(string $type, string $message): string
{
    $valid = ['success', 'error', 'info'];
    $type = in_array($type, $valid, true) ? $type : 'info';
    return "<div class='alert alert-{$type}'>{$message}</div>";
}

function safeInput(?string $value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function statusBadge(string $status): string
{
    $class = strtolower($status) === 'active' ? 'success' : 'warning';
    return "<span class='badge badge-{$class}'>{$status}</span>";
}

function getAdminCourseSelect(PDO $pdo, ?int $selected = null): string
{
    $courses = getAllCourses($pdo);
    $options = ['<option value="">Select Course</option>'];
    foreach ($courses as $course) {
        $selectedAttr = ($selected !== null && (int)$course['id'] === $selected) ? 'selected' : '';
        $options[] = "<option value='{$course['id']}' {$selectedAttr}>{$course['code']} - {$course['name']}</option>";
    }
    return implode('', $options);
}

function getStudentSelect(PDO $pdo, ?int $selected = null): string
{
    $students = getAllStudents($pdo);
    $options = ['<option value="">Select Student</option>'];
    foreach ($students as $student) {
        $selectedAttr = ($selected !== null && (int)$student['id'] === $selected) ? 'selected' : '';
        $options[] = "<option value='{$student['id']}' {$selectedAttr}>{$student['name']} ({$student['email']})</option>";
    }
    return implode('', $options);
}

function isDarkModeEnabled(): bool
{
    return ($_COOKIE['theme'] ?? 'dark') === 'dark';
}

if (!isset($_SESSION['theme'])) {
    $_SESSION['theme'] = 'dark';
}

if (isset($_GET['set_theme']) && in_array($_GET['set_theme'], ['light', 'dark'], true)) {
    $_SESSION['theme'] = $_GET['set_theme'];
    setcookie('theme', $_GET['set_theme'], time() + (60 * 60 * 24 * 365), '/');
}

?>
