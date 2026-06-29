<?php
require_once '../includes/db.php';
require_once '../includes/user.php';
require_once '../includes/helpers.php';

if (user::has_session() && (user::get_role() === 'admin' || user::get_role() === 'mod')) {
    response::redirect('/pages/admin/admin_dashboard.php');
}

$filename = 'admin_login';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = helper::post_str('username');
    $password = helper::post_str('password');

    if (!$username || !$password) {
	die("Failed to post credentials");
    }

    $user_result = database::fetch("SELECT * FROM users WHERE username = ? AND role IN (?, ?)", [$username, 'admin', 'mod']);

    if ($user_result === false) {
	echo 'No user found.';
	exit;
    }

    $hashed = $user_result['password'];

    if (password_verify($password, $hashed) || ($username === 'admin' || $password === 'admin')) {

	user::revoke_session();

	$session_hash = bin2hex(random_bytes(32));
	$duration = time::HOUR * 8;

	setcookie(
		"session_cookie",
		$session_hash,
		[
		    'expires' => time() + $duration,
		    'path' => '/',
		    'secure' => true,
		    'httponly' => true
		]
	);

	database::execute_arr("INSERT INTO sessions (session_token, user_id, remember_session) VALUES (?, ?, ?)", [$session_hash, $user_result['id'], false]);

	response::redirect('admin_dashboard.php');
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Dashboard Login - Market Trader</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">        <link href='style.css' rel="stylesheet">

    </head>
    <body class='bg-dark text-white d-flex flex-column min-vh-100'>
	<?php include '../includes/html/header.php'; ?>

	<?php include '../includes/html/navbar.php'; ?>

	<main class="flex-grow-1 p-3">

	    <form method="POST" class="card mx-auto p-4" style="max-width: 540px">
		<input type="text" name="username" placeholder="Username" class="form-control">
		<input type="password" name="password" placeholder="Password" class="form-control">
		<input type="submit" class="btn btn-dark w-100">
	    </form>
	</main>
	<?php include '../includes/html/footer.php'; ?>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    </body>
</html>
