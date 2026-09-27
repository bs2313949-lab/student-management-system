<?php
require_once __DIR__ . '/db.php';
requireLogin(['admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_course'])) {
    $code = trim($_POST['code'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $credits = (int) ($_POST['credits'] ?? 0);
    $description = trim($_POST['description'] ?? '');

    if ($code && $name && $credits > 0) {
        $existing = $pdo->prepare('SELECT id FROM courses WHERE code = :code LIMIT 1');
        $existing->execute([':code' => $code]);
        if ($existing->fetch()) {
            $pdo->prepare('UPDATE courses SET name = :name, credits = :credits, description = :description WHERE code = :code')->execute([
                ':name' => $name,
                ':credits' => $credits,
                ':description' => $description,
                ':code' => $code,
            ]);
            $successMessage = 'Course updated successfully.';
        } else {
            $pdo->prepare('INSERT INTO courses (code, name, credits, description) VALUES (:code, :name, :credits, :description)')->execute([
                ':code' => $code,
                ':name' => $name,
                ':credits' => $credits,
                ':description' => $description,
            ]);
            $successMessage = 'Course added successfully.';
        }
    } else {
        $errorMessage = 'Please enter valid course info.';
    }
}

if (isset($_GET['delete_id'])) {
    $courseId = (int) $_GET['delete_id'];
    $pdo->prepare('DELETE FROM courses WHERE id = :id')->execute([':id' => $courseId]);
    redirect('courses.php');
}

$courses = getAllCourses($pdo);

ob_start();
?>
<?= navLinks() ?>
<main class='container'>
    <div class='page-head'>
        <div>
            <p class='eyebrow'>Academics</p>
            <h1>Courses</h1>
        </div>
    </div>

    <?php if (isset($successMessage)): ?>
        <?= alertBox('success', $successMessage) ?>
    <?php endif; ?>

    <?php if (isset($errorMessage)): ?>
        <?= alertBox('error', $errorMessage) ?>
    <?php endif; ?>

    <section class='panel'>
        <div class='panel-head'>
            <h2>Add / Update Course</h2>
        </div>
        <form method='POST' class='form-grid'>
            <input type='hidden' name='save_course' value='1'>

            <label>
                Course Code
                <input type='text' name='code' required>
            </label>

            <label>
                Course Name
                <input type='text' name='name' required>
            </label>

            <label>
                Credits
                <input type='number' name='credits' min='1' required>
            </label>

            <label class='full-width'>
                Description
                <textarea name='description' rows='4'></textarea>
            </label>

            <div class='full-width form-actions'>
                <button type='submit' class='btn btn-primary'>Save Course</button>
            </div>
        </form>
    </section>

    <section class='panel'>
        <div class='panel-head'>
            <h2>Course List</h2>
        </div>
        <div class='table-wrap'>
            <table>
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Credits</th>
                        <th>Description</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($courses as $course): ?>
                        <tr>
                            <td><?= safeInput($course['code']) ?></td>
                            <td><?= safeInput($course['name']) ?></td>
                            <td><?= (int) $course['credits'] ?></td>
                            <td><?= safeInput($course['description'] ?? 'N/A') ?></td>
                            <td>
                                <div class='inline-actions'>
                                    <a href='courses.php?delete_id=<?= (int) $course['id'] ?>' class='btn btn-small btn-danger' onclick="return confirm('Delete this course?');">Delete</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>
<?php
renderLayout('Courses', navLinks() . ob_get_clean());
?>
