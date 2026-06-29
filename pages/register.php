<!DOCTYPE html>
<?php
require_once '../includes/db.php';
require_once '../includes/user.php';
require_once '../includes/helpers.php';

session_start();

if (user::has_session()) {
    header('Location: ../index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = helper::post_str('email', FILTER_VALIDATE_EMAIL);
    $password = helper::post_str('password');
    $username = helper::post_str('username');
    $display_name = helper::post_str('display-name');
    $location = helper::post_str('location');
    $user_avatar = $_FILES['user-avatar'] ?? null;

    if ($email === false || $password === false) {
	echo 'post fail';
	exit;
    }

    $result = database::fetch("SELECT * FROM users WHERE email = ?", [$email]);

    if ($result !== false) {
	echo 'email already in use.';
	exit;
    } else {
	$hashed = password_hash($password, PASSWORD_DEFAULT);

	$file_info = img_helper::process_img($user_avatar);
	$dest = '../uploads/profile_pics/' . $file_info['new_name'];

	if (!move_uploaded_file($_FILES['user-avatar']['tmp_name'], $dest)) {
	    die("Failed to properly save avatar");
	}

	$pdo = database::connect();
	$stmt = $pdo->prepare("INSERT INTO users (email, password, username, display_name, location, avatar) VALUES (?, ?, ?, ?, ?, ?)");
	$stmt->execute([$email, $hashed, $username, $display_name, $location, $file_info['new_name']]);

	response::redirect('../index.php');
    }
}
?>
<html>
    <head>
        <meta charset="UTF-8" content='width=device-width, initial-scale=1'>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">        <link rel="stlesheet" href="../style.css">
        <title>Create Account</title>
    </head>
    <body class="text-white bg-dark d-flex flex-column min-vh-100">
	<?php require_once '../includes/html/header.php'; ?>

	<main class="flex-grow-1 p-3">
	    <div class='card mx-auto p-4' style='max-width: 540px'>
		<form method='post' enctype="multipart/form-data">
		    <h3 class='h3'>Account Details:</h3>
		    <div class='mb-3'>
			<label for="email" class='form-label'>Email:</label><br>
			<input type="email" name="email" class='form-control'>
		    </div> 

		    <div class='mb-3'>
			<label for="password" class='form-label'>Password:</label><br>
			<input type="password" name="password" class='form-control'>
		    </div>

		    <h3 class='h3'>Profile Details:</h3>
		    <div class='mb-3'>
			<input type='text' name='username' placeholder='username' class='form-control'>
			<input type='text' name='display-name' placeholder='Display name' class='form-control'>
			<input type='text' name='address' placeholder='Address' class='form-control'>

			<label class='btn btn-outline-secondary w-100'>Upload Avatar
			    <input type='file' name='user-avatar' placeholder='Avatar' accept='.png, .jpg, .webp' hidden>
			</label>

			<input type="submit" class='btn btn-dark w-100'>

		    </div>


		</form>
	    </div>
	</main>

	<?php require_once '../includes/html/footer.php'; ?>
    </body>
</html>
