<?php
if (user::has_session()) {
    response::redirect('../index.php');
}

$session_cookie = user::get_session_token();
$user_id = database::fetch("SELECT * FROM sessions WHERE session_token = ?", [$session_cookie]);
$user = database::fetch("SELECT * FROM users WHERE id = ?", [$user_id]);

$ban_info = database::fetch("SELECT * FROM bans WHERE user_id = ?", [$user_id]);

$ban_message = null;
if ($ban_info['permanent'] === '1' || $ban_info['banned_until'] === null) {
    $ban_message = 'permanently banned';
} else {
    $ban_message = "temporarily banned until: " . strtotime($ban_info);
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>

        <h3><?= "You have been " . htmlspecialchars($ban_message) ?>
	    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>    
    </body>
</html>
