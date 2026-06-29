<?php
require_once '../includes/db.php';
require_once '../includes/helpers.php';
require_once '../includes/user.php';

session_start();

if (!user::has_session()) {
    response::redirect('login.php');
}

$filename = 'profile';

$user_id = helper::get_str('id');
$owns_page = false;

$new_desc = helper::fetch_post('new-desc');
$new_addr = helper::fetch_post('new-addr');

$orders = null;
$listings = null;

if ($user_id === false || $user_id === null) {
    $user_id = 'self';
}

if ($user_id === 'self' || $user_id === user::get_user_id()) {
    $user_id = user::get_user_id();
    $owns_page = true;
    $orders = database::fetchall("SELECT * FROM orders WHERE owner_id = ?", [$user_id]);
    $listings = database::fetchall("SELECT * FROM listings WHERE owner_id = ?", [$user_id]);
} else {
    $listings = database::fetchall("SELECT * FROM listings WHERE owner_id = ?", [$user_id]);
}

$result = database::fetch("SELECT * FROM users WHERE id = ?", [$user_id]);

if ($result === false) {
    die("Failed to fetch user.");
}

$username = $result['username'];
$email = $result['email'];
$avatar = $result['avatar'];
$account_age = time() - strtotime($result['created_at']);
$display_name = $result['display_name'];
$description = $result['bio'];
$score = $result['trust_score'];
$balance = $result['wallet_balance'];

if ($description !== $new_desc) {
    database::execute_arr("UPDATE users SET bio = ? WHERE id = ?", [$new_desc, $user_id]);
}

if ($result['location'] !== $new_addr) {
    database::execute_arr("UPDATE users SET location = ? WHERE id = ?", [$new_addr, $user_id]);
}
?>
<!DOCTYPE html>
<html>
    <head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">        
        <title>Profile - <?= htmlspecialchars($username); ?></title>
    </head>
    <body class='bg-dark text-light d-flex flex-column min-vh-100'>

	<?php include '../includes/html/header.php'; ?>
	<?php include '../includes/html/navbar.php'; ?>

        <div class='container mt-4 flex-grow-1 py-4'>
	    <div class="row">



		<!-- main profile section -->
		<div class="col-7">
		    <div class='card mx-auto p-4 border-light bg-dark text-light' style='max-width: 700px;'>
			<div class='row mb-2'>
			    <div class='col'>
				<img src="../uploads/profile_pics/<?= htmlspecialchars($avatar) ?>" class='rounded' style="max-width: 250px;">
			    </div>

			    <div class='col'>
				<h3 class='h2'><?= htmlspecialchars($display_name); ?></h3>
				<h4 class='h3 text-info'><?= '@' . htmlspecialchars($username); ?></h4>
			    </div>
			</div>

			<div class='row'>
			    <h3 class='h3 text-decoration-underline mb-4'>Profile Details</h3>

			    <div class='card bg-dark border-light mb-4'>
				<div class='card-body'>
				    <p class='text-light'><?= $description === "" ? "No description yet" : htmlspecialchars($description); ?></p>
				</div>
			    </div>
			</div>

			<div class='row'>
			    <div class='col'>
				<p class='text-light'>Status: <?= htmlspecialchars($result['is_verified']); ?></p>
				<p class='text-light'><?= "Trust score: " . htmlspecialchars($score); ?></p>
				<p class='text-light'>Account Age: <?= htmlspecialchars(time::timeAgo($account_age)); ?></p>
			    </div>

			    <div class='col'>
				<?php if ($owns_page === true): ?>
    				<p class='text-light'>Email: <?= htmlspecialchars($email); ?></p>
    				<p class='text-light'>Address: <?= htmlspecialchars($result['location']); ?></p>
    				<p class='text-light'>Balance: R<?= htmlspecialchars($balance); ?></p>
				<?php endif; ?>

			    </div>
			</div>
		    </div>
		</div>

		<!-- description update -->
		<div class="col-5">

		    <?php if ($owns_page): ?>
    		    <form method="post" class='card mx-auto p-4 bg-dark text-white border-white' style='max-width: 540px'>
    			<h3 class='h3 text-decoration-underline mb-4'>Update Profile</h3>

    			<textarea class='form-control mb-2' placeholder='Updated description...' name='new-desc'></textarea>
    			<input type='text' class='form-control mb-2' placeholder='Updated Address' name='new-addr'>

    			<input type='submit' class='btn btn-secondary w-100' value='Update'>
    		    </form>

    		    <div class='max-auto my-2'>
    			<button class='btn btn-warning text-dark border-dark w-100' id='logoutBtn'>Log out</button>
    		    </div>

		    <?php endif; ?>
		</div>
	    </div>


	    <?php if ($owns_page === true): ?>
    	    <!-- user orders -->
    	    <div class='border m-4 p-2'> 

    		<h3 class='h3 text-decoration-underline'>Orders:</h3>

		    <?php if (empty($orders)): ?>
			<h4 class='h4 text-center'>No orders found.</h4>
		    <?php else: ?>
			<?php foreach ($orders as $order): ?>
	    		<div class='card mx-auto p-2 bg-dark text-white border-white mt-2'>
	    		    <div class="row">
	    			<div class="col">
	    			    <h3><?= 'Order: #00' . htmlspecialchars($order['id']); ?></h3>
	    			</div>
	    			<div class="col">
	    			    <h3><?= 'Price: R' . number_format(sprintf('%.2f', $order['price']), 2, '.', ''); ?></h3>
	    			</div>
	    		    </div>
	    		    <div class="row">
	    			<h3><?= 'Status: ' . htmlspecialchars($order['status']); ?></h3>
	    		    </div>
	    		</div>
			<?php endforeach; ?>
		    <?php endif; ?>     

    	    </div>
	    <?php endif; ?>

	    <!-- user listings -->
	    <div class='border m-4 p-2'> 
		<h3 class='h3 text-decoration-underline'>Listings for sale:</h3>
		<?php include '../includes/html/listingblock.php'; ?>
	    </div>


        </div>
	<?php include '../includes/html/footer.php'; ?>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
        <script src='/js/profile.js'></script>
    </body>
</html>
