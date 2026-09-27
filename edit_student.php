<?php
require_once __DIR__ . '/db.php';
requireLogin(['admin', 'student']);

$studentId = (int) ($_GET['id'] ?? 0);
$student = getStudentById($pdo, $studentId);

if (!$student) {
    if (isStudent()) {
        $student = getStudentByUserId($pdo, (int) $_SESSION['user_id']);
    }
}

if (!$student) {
    redirect('dashboard.php');
}

if (isStudent() && (int) $student['user_id'] !== (int) $_SESSION['user_id']) {
    redirect('dashboard.php');
}

$marks = getMarksForStudent($pdo, (int) $student['id']);
$summary = getMarksSummary($pdo, (int) $student['id']);

ob_start();
?>
<?= navLinks() ?>
<main class='container'>
    <div class='page-head'>
        <div>
            <p class='eyebrow'>Student</p>
            <h1><?= safeInput($student['name']) ?>'s Profile</h1>
        </div>
        <?php if (isAdmin()): ?>
            <div class='inline-actions'>
                <a href='edit_student.php?id=<?= (int) $student['id'] ?>' class='btn btn-primary'>Edit</a>
                <a href='students.php' class='btn btn-secondary'>Back</a>
            </div>
        <?php endif; ?>
    </div>

    <section class='panel'>
        <div class='profile-grid'>
            <div>
                <p><strong>Name:</strong> <?= safeInput($student['name']) ?></p>
                <p><strong>Email:</strong> <?= safeInput($student['email']) ?></p>
                <p><strong>Username:</strong> <?= safeInput($student['username'] ?? '') ?></p>
                <p><strong>Phone:</strong> <?= safeInput($student['phone'] ?? 'N/A') ?></p>
            </div>
            <div>
                <p><strong>Course:</strong> <?= safeInput($student['course_name'] ?? 'No course') ?></p>
                <p><strong>Gender:</strong> <?= safeInput($student['gender'] ?? 'N/A') ?></p>
                <p><strong>Date of Birth:</strong> <?= safeInput(formatDate($student['date_of_birth'] ?? null)) ?></p>
                <p><strong>Enrollment Date:</strong> <?= safeInput(formatDate($student['enrollment_date'] ?? null)) ?></p>
            </div>
        </div>
        <div class='mt-2'>
            <p><strong>Address:</strong> <?= safeInput($student['address'] ?? 'N/A') ?></p>
            <p><strong>Status:</strong> <?= statusBadge($student['status'] ?? 'active') ?></p>
        </div>
    </section>

    <section class='panel'>
        <div class='panel-head'>
            <h2>Academic Summary</h2>
        </div>
        <div class='stats-grid small'>
            <div class='stat-card'>
                <span>Average</span>
                <strong><?= number_format((float) ($summary['avg'] ?? 0), 1) ?>%</strong>
            </div>
            <div class='stat-card'>
                <span>Highest</span>
                <strong><?= number_format((float) ($summary['max_score'] ?? 0), 1) ?>%</strong>
            </div>
            <div class='stat-card'>
                <span>Lowest</span>
                <strong><?= number_format((float) ($summary['min_score'] ?? 0), 1) ?>%</strong>
            </div>
            <div class='stat-card'>
                <span>Exams</span>
                <strong><?= (int) ($summary['count'] ?? 0) ?></strong>
            </div>
        </div>
    </section>

    <section class='panel'>
        <div class='panel-head'>
            <h2>Marks</h2>
        </div>
        <div class='table-wrap'>
            <table>
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Exam</th>
                        <th>Marks</th>
                        <th>Percent</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($marks)): ?>
                        <tr><td colspan='4'>No marks recorded.</td></tr>
                    <?php else: ?>
                        <?php foreach ($marks as $mark): ?>
                            <tr>
                                <td><?= safeInput($mark['course_code'] ?? '') ?> - <?= safeInput($mark['course_name'] ?? '') ?></td>
                                <td><?= safeInput($mark['exam_name']) ?></td>
                                <td><?= (int) $mark['mark'] ?>/<?= (int) $mark['total'] ?></td>
                                <td><?= number_format((float) $mark['score_percent'], 1) ?>%</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>
<?php
renderLayout('Student Profile', navLinks() . ob_get_clean());
?>
