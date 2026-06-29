<?php
require_once '../../includes/db.php';
require_once '../../includes/helpers.php';
require_once '../../includes/user.php';

if (!user::has_session() || user::get_role() === false) {
    response::redirect('../../pages/login.php');
}

if (user::get_role() === 'user') {
    response::redirect('../../index.php');
}

$filename = 'admin_dashboard';

$total_users = database::fetch("SELECT COUNT(*) FROM users WHERE is_banned = ?", ["0"]);
$total_listings = database::fetch("SELECT COUNT(*) FROM listings WHERE status = ?", ['active']);
$pending_reports = database::fetch("SELECT COUNT(*) FROM reports WHERE status = ?", ['open']);
$new_signups = database::fetch("SELECT COUNT(*) FROM reports WHERE created_at >= ?", ['NOW() - INTERVAL 1 DAY']);

$all_users = database::fetchall("SELECT * FROM users WHERE role = ?", ['user']);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1">
        <title>Dashboard</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">        <link href='style.css' rel="stylesheet">

    </head>
    <body class="bg-dark text-light d-flex flex-column min-vh-100">

	<?php include '../../includes/html/header.php'; ?>
	<?php include '../../includes/html/navbar.php'; ?>

	<main class="flex-grow-1 p-3">

	    <div class="row">
		<!-- stat readout -->
		<div class='card mx-auto p-4 text-center bg-dark text-white mb-4 w-100'>
		    <div class="d-flex flex-wrap justify-content-center gap-3">
			<div class='px-4 py-2 bg-dark'>
			    <p>Users: <?= htmlspecialchars($total_users['COUNT(*)']); ?></p>
			</div>
			<div class='px-4 py-2 bg-dark'>
			    <p>Listings: <?= htmlspecialchars($total_listings['COUNT(*)']); ?></p>
			</div>
			<div class='px-4 py-2 bg-dark'>
			    <p>Pending Reports: <?= htmlspecialchars($pending_reports['COUNT(*)']); ?></p>
			</div>
			<div class='px-4 py-2 bg-dark'>
			    <p>Signups Today: <?= htmlspecialchars($new_signups['COUNT(*)']); ?></p>
			</div>
		    </div>
		</div>
	    </div>

	    <!-- user readout -->
	    <div class="container">
		<div class="row">
		    <h3 class='h3 text-center'>User Management</h3>

		    <div class="row g-3 mt-3">
			<?php foreach ($all_users as $current_user): ?>
    			<div class="col-md-6 col-lg-4">
    			    <div class='card h-100 p-4 bg-secondary text-white'>
				    <?php $last_login = database::fetch("SELECT created_at FROM sessions WHERE user_id = ?", [$current_user['id']]); ?>
				    <?php
				    $user_listing_amt = database::fetch("SELECT COUNT(*) FROM listings WHERE owner_id = ?", [$current_user['id']]);
				    if ($current_user['is_banned'] === '1') {
					$status = 'banned';
				    } elseif ($current_user['is_verified'] === '1') {
					$status = 'verified';
				    } else {
					$status = 'unverified';
				    }
				    ?>

    				<div class='d-flex gap-2 align-items-center mb-2'>
    				    <img src="/uploads/profile_pics/<?= $current_user['avatar']; ?>" 
    					 class="rounded-circle" 
    					 style="width: 48px; height: 48px; object-fit: cover;">
    				    <div>
    					<h4 class='h4 mb-0'><?= htmlspecialchars($current_user['username']); ?></h4>
    					<h6 class='h6 mb-0 text-muted'>
    					    #<a class='link-primary link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover' 
    						href='/Testproj/pages/profile.php?id=<?= htmlspecialchars($current_user['id']); ?>'>
							<?= htmlspecialchars($current_user['id']); ?>
    					    </a>
    					</h6>
    				    </div>
    				</div>
    				<div class='row text-center'>
    				    <div class='col-6'>
    					<p class='mb-0 text-light small'>Email: <?= htmlspecialchars($current_user['email']); ?></p>
    					<p class='mb-0 text-light small'>Joined: <?= htmlspecialchars($current_user['created_at']); ?></p>
    					<p class='mb-0 text-light small'>Status: <?= htmlspecialchars($status); ?></p>
    				    </div>
    				    <div class='col-6'>
    					<p class='mb-0 text-light small'>Listings Created: <?= htmlspecialchars($user_listing_amt['COUNT(*)']); ?></p>
    					<p class='mb-0 text-light small'>ID: <?= htmlspecialchars($current_user['id']); ?></p>
    				    </div>
    				</div>
    			    </div>
    			</div>

			<?php endforeach; ?>
		    </div>
		</div>
	    </div>
	</main>
	<?php include '../../includes/html/footer.php'; ?>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    </body>
</html>
