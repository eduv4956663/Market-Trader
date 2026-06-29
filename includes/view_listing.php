<?php
require_once 'db.php';
require_once 'helpers.php';
require_once 'user.php';

if (!user::has_session()) {
    response::redirect('../pages/login.php');
}

$listing_id = helper::get_str('id');

if ($listing_id === false) {
    die('invalid id getted');
}

$listing = database::fetch("SELECT * FROM listings WHERE id = ?", [$listing_id]);

if ($listing === false) {
    die("Found no listing with id $listing_id");
}

$owner_id = $listing['owner_id'];
$owner_result = database::fetch("SELECT * FROM users WHERE id = ?", [$owner_id]);

$category_data = database::fetch("SELECT * FROM product_cats WHERE name = ?", [$listing['category']]);

if ($owner_result === false) {
    die("No user found with specified ID.");
}
$owns_listing = $owner_id === user::get_user_id();

$like_data = database::fetch("SELECT * FROM likes WHERE user_id = ? AND listing_id = ?", [user::get_user_id(), $listing_id]);
$likes_listing = false;

if ($like_data !== false) {
    $likes_listing = $like_data['has_liked'] === '1';
}

database::execute_arr("UPDATE listings SET views = views + 1 WHERE id = ?", [$listing_id]);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" content='width=device-width, initial-scale=1'>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
        <title><?= htmlspecialchars($listing['title']); ?> - Market Trader</title>
    </head>
    <body class="bg-dark text-light d-flex flex-column min-vh-100">
	<?php include 'html/header.php'; ?>
	<?php include 'html/navbar.php'; ?>
	<main class="flex-grow-1 p-3">

	    <div class='container'>
		<div class="row py-4 g-7">
		    <div class='<?= $owns_listing ? 'col-8' : 'col' ?>'>
			<div class="card mx-auto bg-secondary text-white border-white p-4" style="max-width: 700px">

			    <div class='row mb-4'>
				<div class='col'>
				    <img src='<?= '../uploads/listing_images/' . htmlspecialchars($listing['image_name']); ?>' class='rounded' style='max-width: 250px;'>
				</div>

				<div class='col'>
				    <div class='card bg-dark text-white border-white mx-auto px-2 py-2 text-center'>
					<h2 class='h2 text-decoration-underline'><?= htmlspecialchars($listing['title']); ?></h2>
					<h4 class='h4'>
					    <i class='<?= $category_data['icon_class']; ?>'></i> <?= htmlspecialchars($category_data['name']); ?>
					</h4>
				    </div>
				</div>

			    </div>

			    <div class='row'>

				<div class='card bg-dark text-white mx-auto px-2 py-2'>

				    <a class='link-white h3' href='/pages/profile.php?id=<?= $owner_id; ?>'>@<?= htmlspecialchars($owner_result['username']); ?></a>

				    <div class='card bg-white text-black border-white mx-auto py-4 w-100 mb-2'>
					<p class='text-white'><?= htmlspecialchars($listing['description']); ?></p>
				    </div>

				    <div class='row'>
					<div class='col'>
					    <p>Views: <?= htmlspecialchars($listing['views']); ?></p>
					    <p>Favourites: <?= htmlspecialchars($listing['favourites']); ?></p>
					    <p>Created: <?= time::timeAgo(time() - strtotime($listing['created_at'])); ?> ago.</p>
					</div>

					<div class='col'>
					    <div class='row'>
						<?php if ($listing['status'] === 'active'): ?>
    						<p class='text-success h4 text-center'>Status: Active</p>
						<?php endif; ?>

						<?php if ($listing['status'] === 'inactive'): ?>
    						<p class='text-warning h4 text-center'>Status: Inactive</p>
						<?php endif; ?>

						<?php if ($listing['status'] === 'reserved'): ?>
    						<p class='text-danger h4 text-center'>Status: Reserved</p>
						<?php endif; ?>
					    </div>
					    <?php if (!$owns_listing): ?>
    					    <div class='row py-2'>

    						<div class='col d-flex '>


    						    <div class='row py-2'>
    							<div class='col-8'>
    							    <button class='btn btn-info border-white text-dark text-center mx-auto' id='addToCart'>Add to cart</button>
    							</div>

    							<div class='col-4'>
								<?php if ($likes_listing): ?>
								    <button class='btn btn-success text-white mx-2'>Unfavourite</button>
								<?php else: ?>
								    <button class='btn btn-secondary text-white mx-2'>Favourite</button>
								<?php endif; ?>
    							</div>

    						    </div>


    						</div>

    						<div class='row mx-auto text-center'>
    						    <p class='h2' id='notify' hidden>Placeholder</p>
    						</div>
						<?php endif; ?>
					    </div>

					</div>
				    </div>

				</div>

			    </div>
			</div>
			<?php if ($owns_listing): ?>
    			<div class="col-4">

    			    <form method ='post' class="card mx-auto bg-dark text-white p-4">
    				<input type='text' name='new_name' placeholder='Edit title' class="form-control mb-2">
    				<textarea rows='3' name='new_description' placeholder='Updated description' class="form-control mb-4"></textarea>


    				<label for='new_status_active' class="form-check-label" checked>
    				    <input type='radio' id='new_status_active' name='new_status' value='active' class='form-check-input mb-2'>Active
    				</label>


    				<label for='new_status_active' class="form-check-label">
    				    <input type='radio' id='new_status_inactive' name='new_status' value='inactive' class='form-check-input mb-4'>
    				    Inactive
    				</label>

    				<input type='submit' class='btn btn-secondary text-dark w-100'>
    			    </form>

    			</div>
			<?php endif; ?>
		    </div>
		</div>
	    </div>
	</main>


	<?php include 'html/footer.php'; ?>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script> 
        <script src="/js/view_listing.js"></script>
    </body>
</html>
