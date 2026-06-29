<?php
require_once '../includes/db.php';
require_once '../includes/helpers.php';
require_once '../includes/user.php';
session_start();

$filename = 'login';

if (user::has_session()) {
    response::redirect('../index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = helper::post_str('email', FILTER_VALIDATE_EMAIL);
    $password = helper::post_str('password', FILTER_DEFAULT);

    $should_remember = helper::post_str('remember', FILTER_DEFAULT) === 'yes' ? true : false;

    $email = $_POST['email'];

    if (!$email || !$password) {
	die('Failed to post email and/or password.');
    }

    $identical_creds = false;

    if ($email === $password) {
	$identical_creds = true;
    }

    $user = database::fetch("SELECT * FROM users WHERE email = ?", [$email]);

    if ($user === false) {
	echo 'No user found';
	exit;
	if ($identical_creds) {
	    die("No account registered to $email. Email and password must not match.");
	} else {
	    die("No account registered to $email.");
	}
    }

    $user_id = $user['id'];

    // clear remaining session
    $pdo = database::connect();
    $stmt = $pdo->prepare("DELETE FROM sessions WHERE user_id = ?");
    $stmt->execute([$user_id]);

    if (password_verify($password, $user['password'],)) {
	$session_hash = bin2hex(random_bytes(32));

	$duration = $should_remember === true ? (time::MONTH * 1) : (time::HOUR * 8);

	setcookie(
		"session_cookie",
		$session_hash,
		[
		    "expires" => time() + $duration,
		    "path" => "/",
		    "secure" => "true",
		    "httponly" => "true"
		]
	);

	$stmt = $pdo->prepare("INSERT INTO sessions (user_id, session_token, remember_session) VALUES (?, ?, ?)");
	$stmt->execute([$user_id, $session_hash, $should_remember]);

	response::redirect('../index.php');
    } else {
	echo 'Incorrect username or password.';
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" content='width=device-width, initial-scale=1'>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">        <link rel="stylesheet" href="../style.css">
        <title>Log in</title>
    </head>
    <body class='bg-dark text-light d-flex flex-column min-vh-100'>
	<?php include '../includes/html/header.php'; ?>

	<main class="flex-grow-1 p-3">

	    <form method="POST" class="card mx-auto p-4" style="max-width: 540px">
		<input type="email" name="email" placeholder="email" class='form-control'>

		<input type="password" name="password" placeholder="password" class='form-control'>

		<br>

		<div class='form-check'>
		    <input type="checkbox" name="remember" value='yes' class="form-check-input">
		    <label for="remember" class="form-check-label">Remember me</label>
		</div>


		<input type="submit" value="Log in" class="btn btn-dark w-100">
	    </form>

	    <div class="text-center">
		<a href="register.php" class="link-primary link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">Don&apos;t have an account?</a>
	    </div>

	</main>

	<?php include '../includes/html/footer.php'; ?>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>    </body>
</html>
