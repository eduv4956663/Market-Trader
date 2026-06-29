<?php
require_once '../../includes/db.php';
require_once '../../includes/user.php';
require_once '../../includes/helpers.php';

if (!user::has_session()) {
    response::redirect('../../index.php');
}

if ($user_role = user::get_role() !== 'admin') {
    if ($user_role === 'mod') {
	response::redirect('admin_dashboard.php');
    } else {
	response::redirect('../../index.php');
    }
}

$filename = 'create_staff';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = helper::fetch_post('username');
    $email = helper::fetch_post('email');
    $password = helper::fetch_post('password');
    $display_name = helper::fetch_post('display-name');
    $role = helper::fetch_post('role');

    $bio = $role === 'admin' ? 'An administrator of site' : 'A moderator of site';

    $email_response = database::fetch("SELECT * FROM users WHERE email = ?", [$email]);
    if ($email_response !== false) {
	die("Email is already in use.");
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);

    database::execute_arr("INSERT INTO users (username, display_name, email, password, role, location, bio, is_verified) VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
	    [$username, $display_name, $email, $hashed, $role, 'N/A', $bio, true]);

    response::redirect('admin_dashboard.php');
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Create New Staff</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">        <link rel="stylesheet" href="../style.css">

    </head>
    <body class="bg-dark text-white d-flex flex-column min-vh-100">

	<?php include '../../includes/html/header.php'; ?>

	<?php include '../../includes/html/navbar.php'; ?>

	<main class="flex-grow-1 p-3">

	    <div class='card mx-auto p-4' style='max-width: 540px'>
		<form method='post'>

		    <div class='mb-3 text-center'>
			<h3 class='h3'>Create new staff account.</h3>
		    </div>

		    <div class='mb-3'>
			<input type='text' name='username' placeholder='Username' class='form-control'>
			<input type='text' name='display-name' placeholder='Display Name' class='form-control'>
			<input type='password' name='password' placeholder="Password" class="form-control">
			<label for="role" class="form-label">Staff Role: </label>
			<select name="staff-role" id="role" class="form-select">
			    <option value="admin">Admin</option>
			    <option value="mod">Moderator</option>
			</select>

			<input type="submit" class="btn btn-secondary w-100" value="Create">
		    </div>

		</form>
	    </div>

	</main>

	<?php include '../../includes/html/footer.php'; ?>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>    </body>
</body>
</html>
