<?php
require_once __DIR__ . '/db.php';

session_start();
session_unset();
session_destroy();
redirect('login.php');
