<?php
require_once __DIR__ . '/db.php';

if (!empty($_SESSION['user_id'])) {
    redirect('dashboard.php');
}

redirect('login.php');
