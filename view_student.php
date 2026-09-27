<?php
require_once __DIR__ . '/db.php';
requireLogin(['admin', 'student']);

if (isAdmin()) {
    if (isset($_GET['delete_id'])) {
        $studentId = (int) $_GET['delete_id'];
        deleteStudent($pdo, $studentId);
        redirect('students.php');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_student'])) {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $courseId = $_POST['course_id'] ?? null;
        $phone = trim($_POST['phone'] ?? '');
        $gender = trim($_POST['gender'] ?? '');
        $dateOfBirth = trim($_POST['date_of_birth'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $enrollmentDate = trim($_POST['enrollment_date'] ?? date('Y-m-d'));
        $status = trim($_POST['status'] ?? 'active');

        if ($name && $email && $username && $password) {
            createStudentRecord($pdo, [
                'name' => $name,
                'email' => $email,
                'username' => $username,
                'password' => $password,
                'course_id' => $courseId ?: null,
                'phone' => $phone,
                'gender' => $gender,
                'date_of_birth' => $dateOfBirth,
                'address' => $address,
                'enrollment_date' => $enrollmentDate,
                'status' => $status,
            ]);
            $successMessage = 'Student added successfully.';
        } else {
            $errorMessage = 'Please fill all required fields.';
        }
    }
}

$students = getAllStudents($pdo);
$student = isStudent() ? getStudentByUserId($pdo, (int) $_SESSION['user_id']) : null;

ob_start();
?>
<?= navLinks() ?>
<main class='container'>
    <div class='page-head'>
        <div>
            <p class='eyebrow'>Records</p>
            <h1>Students</h1>
        </div>
        <?php if (isAdmin()): ?>
            <a href='students.php?new=1' class='btn btn-primary'>Add Student</a>
        <?php endif; ?>
    </div>

    <?php if (isset($successMessage)): ?>
        <?= alertBox('success', $successMessage) ?>
    <?php endif; ?>

    <?php if (isset($errorMessage)): ?>
        <?= alertBox('error', $errorMessage) ?>
    <?php endif; ?>

    <?php if (isAdmin() && isset($_GET['new'])): ?>
        <section class='panel'>
            <div class='panel-head'>
                <h2>Add New Student</h2>
            </div>
            <form method='POST' class='form-grid'>
                <input type='hidden' name='submit_student' value='1'>

                <label>
                    Full Name
                    <input type='text' name='name' required>
                </label>

                <label>
                    Email
                    <input type='email' name='email' required>
                </label>

                <label>
                    Username
                    <input type='text' name='username' required>
                </label>

                <label>
                    Password
                    <input type='password' name='password' required>
                </label>

                <label>
                    Phone
                    <input type='text' name='phone'>
                </label>

                <label>
                    Gender
                    <select name='gender'>
                        <option value=''>Select</option>
                        <option value='Male'>Male</option>
                        <option value='Female'>Female</option>
                        <option value='Other'>Other</option>
                    </select>
                </label>

                <label>
                    Date of Birth
                    <input type='date' name='date_of_birth'>
                </label>

                <label>
                    Enrollment Date
                    <input type='date' name='enrollment_date' value='<?= date('Y-m-d') ?>'>
                </label>

                <label>
                    Course
                    <select name='course_id'>
                        <?= getAdminCourseSelect($pdo) ?>
                    </select>
                </label>

                <label>
                    Status
                    <select name='status'>
                        <option value='active'>Active</option>
                        <option value='inactive'>Inactive</option>
                    </select>
                </label>

                <label class='full-width'>
                    Address
                    <textarea name='address' rows='4'></textarea>
                </label>

                <div class='full-width form-actions'>
                    <button type='submit' class='btn btn-primary'>Save Student</button>
                    <a href='students.php' class='btn btn-secondary'>Cancel</a>
                </div>
            </form>
        </section>
    <?php else: ?>
        <section class='panel'>
            <div class='panel-head'>
                <h2>Student List</h2>
            </div>
            <div class='table-wrap'>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Course</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isStudent()): ?>
                            <tr>
                                <td><?= safeInput($student['name'] ?? '') ?></td>
                                <td><?= safeInput($student['email'] ?? '') ?></td>
                                <td><?= safeInput($student['course_name'] ?? 'No course') ?></td>
                                <td><?= statusBadge($student['status'] ?? 'active') ?></td>
                                <td>
                                    <a href='view_student.php?id=<?= (int) ($student['id'] ?? 0) ?>' class='btn btn-small btn-secondary'>View</a>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($students as $studentRow): ?>
                                <tr>
                                    <td><?= safeInput($studentRow['name']) ?></td>
                                    <td><?= safeInput($studentRow['email']) ?></td>
                                    <td><?= safeInput($studentRow['course_name'] ?? 'No course') ?></td>
                                    <td><?= statusBadge($studentRow['status'] ?? 'active') ?></td>
                                    <td>
                                        <div class='inline-actions'>
                                            <a href='view_student.php?id=<?= (int) $studentRow['id'] ?>' class='btn btn-small btn-secondary'>View</a>
                                            <a href='edit_student.php?id=<?= (int) $studentRow['id'] ?>' class='btn btn-small btn-primary'>Edit</a>
                                            <a href='students.php?delete_id=<?= (int) $studentRow['id'] ?>' class='btn btn-small btn-danger' onclick="return confirm('Delete this student?');">Delete</a>
                                        </div>
                                    </td>
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
renderLayout('Students', navLinks() . ob_get_clean());
?>
