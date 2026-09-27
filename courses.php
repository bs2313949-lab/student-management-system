<?php
require_once __DIR__ . '/db.php';
requireLogin(['admin']);

$studentId = (int) ($_GET['id'] ?? 0);
$student = getStudentById($pdo, $studentId);
if (!$student) {
    redirect('students.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_student'])) {
    updateStudentRecord($pdo, $studentId, [
        'user_id' => $student['user_id'],
        'name' => trim($_POST['name'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'course_id' => $_POST['course_id'] ?? null,
        'phone' => trim($_POST['phone'] ?? ''),
        'gender' => trim($_POST['gender'] ?? ''),
        'date_of_birth' => trim($_POST['date_of_birth'] ?? ''),
        'address' => trim($_POST['address'] ?? ''),
        'enrollment_date' => trim($_POST['enrollment_date'] ?? date('Y-m-d')),
        'status' => trim($_POST['status'] ?? 'active'),
    ]);
    redirect('view_student.php?id=' . $studentId);
}

ob_start();
?>
<?= navLinks() ?>
<main class='container'>
    <div class='page-head'>
        <div>
            <p class='eyebrow'>Records</p>
            <h1>Edit Student</h1>
        </div>
    </div>

    <section class='panel'>
        <form method='POST' class='form-grid'>
            <input type='hidden' name='update_student' value='1'>

            <label>
                Full Name
                <input type='text' name='name' value='<?= safeInput($student['name']) ?>' required>
            </label>

            <label>
                Email
                <input type='email' name='email' value='<?= safeInput($student['email']) ?>' required>
            </label>

            <label>
                Phone
                <input type='text' name='phone' value='<?= safeInput($student['phone'] ?? '') ?>'>
            </label>

            <label>
                Gender
                <select name='gender'>
                    <option value=''>Select</option>
                    <option value='Male' <?= ($student['gender'] ?? '') === 'Male' ? 'selected' : '' ?>>Male</option>
                    <option value='Female' <?= ($student['gender'] ?? '') === 'Female' ? 'selected' : '' ?>>Female</option>
                    <option value='Other' <?= ($student['gender'] ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
                </select>
            </label>

            <label>
                Date of Birth
                <input type='date' name='date_of_birth' value='<?= safeInput($student['date_of_birth'] ?? '') ?>'>
            </label>

            <label>
                Enrollment Date
                <input type='date' name='enrollment_date' value='<?= safeInput($student['enrollment_date'] ?? date('Y-m-d')) ?>'>
            </label>

            <label>
                Course
                <select name='course_id'>
                    <?= getAdminCourseSelect($pdo, (int) ($student['course_id'] ?? 0)) ?>
                </select>
            </label>

            <label>
                Status
                <select name='status'>
                    <option value='active' <?= ($student['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value='inactive' <?= ($student['status'] ?? 'active') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </label>

            <label class='full-width'>
                Address
                <textarea name='address' rows='4'><?= safeInput($student['address'] ?? '') ?></textarea>
            </label>

            <div class='full-width form-actions'>
                <button type='submit' class='btn btn-primary'>Update Student</button>
                <a href='view_student.php?id=<?= (int) $studentId ?>' class='btn btn-secondary'>Cancel</a>
            </div>
        </form>
    </section>
</main>
<?php
renderLayout('Edit Student', navLinks() . ob_get_clean());
?>
