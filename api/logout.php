<?php

require_once '../includes/db.php';
require_once '../includes/helpers.php';
require_once '../includes/user.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = user::get_session_token();

    database::execute_arr("DELETE FROM sessions WHERE session_token = ?", [$token]);
    response::redirect('../pages/login.php');
}

response::redirect('../index.php');
