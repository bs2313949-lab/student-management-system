<?php
require_once __DIR__ . '/db.php';
requireLogin(['admin', 'student']);

$stats = getDashboardStats($pdo);
$recentStudents = getRecentStudents($pdo);

if (isStudent()) {
    $student = getStudentByUserId($pdo, (int) $_SESSION['user_id']);
    $studentId = $student ? (int) $student['id'] : null;
    $marks = $studentId ? getMarksForStudent($pdo, $studentId) : [];
    $summary = $studentId ? getMarksSummary($pdo, $studentId) : ['count' => 0, 'avg' => 0, 'max_score' => 0, 'min_score' => 0];
}

ob_start();
?>
<?= navLinks() ?>
<main class='container'>
    <div class='page-head'>
        <div>
            <p class='eyebrow'>Overview</p>
            <h1>Dashboard</h1>
        </div>
    </div>

    <?php if (isAdmin()): ?>
        <section class='stats-grid'>
            <div class='stat-card'>
                <span>Total Students</span>
                <strong><?= (int) $stats['students'] ?></strong>
            </div>
            <div class='stat-card'>
                <span>Total Courses</span>
                <strong><?= (int) $stats['courses'] ?></strong>
            </div>
            <div class='stat-card'>
                <span>Total Users</span>
                <strong><?= (int) $stats['users'] ?></strong>
            </div>
            <div class='stat-card'>
                <span>Average Score</span>
                <strong><?= number_format((float) $stats['avg_mark'], 1) ?>%</strong>
            </div>
        </section>

        <section class='panel'>
            <div class='panel-head'>
                <h2>Recent Students</h2>
                <a href='students.php' class='btn btn-primary'>View all</a>
            </div>

            <div class='table-wrap'>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Course</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentStudents as $student): ?>
                            <tr>
                                <td><?= safeInput($student['name']) ?></td>
                                <td><?= safeInput($student['email'] ?? '') ?></td>
                                <td><?= safeInput(($student['course_name'] ?? 'No course')) ?></td>
                                <td><?= statusBadge($student['status'] ?? 'active') ?></td>
                                <td><a href='view_student.php?id=<?= (int) $student['id'] ?>' class='btn btn-small btn-secondary'>View</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    <?php else: ?>
        <section class='stats-grid'>
            <div class='stat-card'>
                <span>Academic Average</span>
                <strong><?= number_format((float) $summary['avg'], 1) ?>%</strong>
            </div>
            <div class='stat-card'>
                <span>Exams</span>
                <strong><?= (int) $summary['count'] ?></strong>
            </div>
            <div class='stat-card'>
                <span>Best Score</span>
                <strong><?= number_format((float) $summary['max_score'], 1) ?>%</strong>
            </div>
            <div class='stat-card'>
                <span>Lowest Score</span>
                <strong><?= number_format((float) $summary['min_score'], 1) ?>%</strong>
            </div>
        </section>

        <section class='panel'>
            <div class='panel-head'>
                <h2>Your Profile</h2>
                <a href='view_student.php?id=<?= (int) $student['id'] ?>' class='btn btn-primary'>Profile</a>
            </div>
            <div class='profile-box'>
                <p><strong>Name:</strong> <?= safeInput($student['name'] ?? '') ?></p>
                <p><strong>Email:</strong> <?= safeInput($student['email'] ?? '') ?></p>
                <p><strong>Course:</strong> <?= safeInput($student['course_name'] ?? 'No course') ?></p>
                <p><strong>Status:</strong> <?= statusBadge($student['status'] ?? 'active') ?></p>
            </div>
        </section>

        <section class='panel'>
            <div class='panel-head'>
                <h2>Your Marks</h2>
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
                            <tr><td colspan='4'>No marks recorded yet.</td></tr>
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
    <?php endif; ?>
</main>
<?php
renderLayout('Dashboard', navLinks() . ob_get_clean());
?>
