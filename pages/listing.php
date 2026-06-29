<?php

require_once '../includes/db.php';
require_once '../includes/helpers.php';
require_once '../includes/user.php';
session_start();

if (!user::has_session()) {
    response::redirect('login.php');
}
$filename = 'listing';

$action = helper::get_str('action');

if ($action === false) {
    die("Incorrect action");
}

switch ($action) {
    case 'create':
	$title = 'Create Listing';
	require_once '../includes/create_listing.php';
	break;

    case 'view':
	$title = 'View Listing';
	require_once '../includes/view_listing.php';
	break;

    default:
	response::redirect('fallthrough.php');
	break;
}

exit;
?>