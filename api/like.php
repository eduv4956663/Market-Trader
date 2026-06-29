<?php

require_once 'includes/db.php';
require_once 'includes/user.php';
require_once 'includes/helpers.php';

header('Content-Type: application/json');

if (!user::has_session()) {
    echo json_encode([
	'error' => 'Not logged in'
    ]);
    exit;
}

$listing_id = helper::post_str('listing_id');
$user_id = user::get_user_id();

$like_data = database::fetch("SELECT * FROM likes WHERE user_id = ? AND listing_id = ?", [$user_id, $listing_id]);

if ($like_data === false) {
    database::execute_arr("INSERT INTO likes (user_id, listing_id) VALUES (?, ?)", [$user_id, $listing_id]);
    $like_count = database::fetch("SELECT COUNT(*) FROM likes WHERE ?", ['1']);

    echo json_encode([
	'new_state' => true,
	'count' => $like_count['COUNT(*)']
    ]);
    exit;
}

$has_liked = $like_data['has_liked'] === '1' ? true : false;

database::execute_arr("UPDATE likes SET has_liked = ?", [!$has_liked]);
$like_count = database::fetch("SELECT COUNT(*) FROM likes WHERE ?", ['1=1']);

echo json_encode([
    'new_state' => !$has_liked,
    'count' => $like_count['COUNT(*)']
]);
exit;
