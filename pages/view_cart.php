<?php
require_once '../includes/user.php';
require_once '../includes/db.php';
require_once '../includes/helpers.php';
require_once '../includes/payment.php';

$filename = 'view_cart';

if (!user::has_session()) {
    response::redirect('/pages/login.php');
}
$user_id = (string) user::get_user_id();

$current_cart = database::fetch("SELECT * FROM carts WHERE user_id = ? AND status = ?", [$user_id, 'open']);
$has_cart = $current_cart !== false;

$cart_items = false;

if ($current_cart) {
    $cart_items = database::fetchall("SELECT * FROM cart_items WHERE cart_id = ?", [$current_cart['id']]);
}

if ($cart_items) {

    $total = 0.0;
    foreach ($cart_items as $item) {
	$item = database::fetch("SELECT * FROM listings WHERE id = ?", [$item['listing_id']]);
	$total += (float) $item['price'];
    }

    $cart_id = $current_cart['id'];
    $user_data = database::fetch("SELECT * FROM users WHERE id = ?", [$user_id]);

    if ($user_data === false) {
	die("No user for some reason.");
    }

    $cart_data = array(
	'merchant_id' => '10049767',
	'merchant_key' => '175e72k4f6om4',
	'return_url' => "https://market-trader.rf.gd/pages/profile.php?id=self",
	'cancel_url' => 'https://market-trader.rf.gd/index.php',
	'notify_url' => 'https://market-trader.rf.gd/api/payout.php',
	'email_address' => $user_data['email'],
	'm_payment_id' => $cart_id,
	'amount' => number_format(sprintf('%.2f', $total), 2, '.', ''),
	'item_name' => "Order: #$cart_id"
    );

    $signature = payment::generateSignature($cart_data, merchant::$passphrase);
    $cart_data['signature'] = $signature;

    $testingMode = true;
    $pfHost = $testingMode ? 'sandbox.payfast.co.za' : 'www.payfast.co.za';
    $htmlForm = '<form action="https://' . $pfHost . '/eng/process" method="post">';
    foreach ($cart_data as $name => $value) {
	$htmlForm .= '<input name="' . $name . '" type="hidden" value=\'' . $value . '\' />';
    }
    $htmlForm .= '<input type="submit" value="Pay Now" class="btn btn-success text-white border-white" /></form>';
}
?>
<!DOCTYPE html>
<html>
    <head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

        <title><?= $has_cart ? '#' . $current_cart['id'] : 'Cart' ?></title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">   


    </head>
    <body class="bg-dark text-light d-flex flex-column min-vh-100">
	<?php require_once '../includes/html/header.php'; ?>
	<?php require_once '../includes/html/navbar.php'; ?>

	<main class="flex-grow-1 p-3">
	    <div class="container">

		<div class="row">
		    <div class="col">

			<div class="card bg-dark text-white border-white p-4 mx-auto">
			    <?php if ($has_cart && $cart_items): ?>
    			    <!-- cart data -->
    			    <div class="row">
    				<div class="col">
    				    <div class="row">
    					<p class="h2 text-white text-decoration-underline"><?= 'Cart: #' . $current_cart['id']; ?></p>
    				    </div>
    				</div>
    			    </div>

    			    <!-- order list -->
    			    <div class="row">
    				<div class="col">
					<?php if ($cart_items === false): ?>
					    <p class="h3 text-white text-center">Your cart is empty. <a href="/pages/browse.php" class="link-white text-center">Browse listings.</a></p>
					<?php else: ?>

					    <?php foreach ($cart_items as $item): ?>
						<?php $listing = database::fetch("SELECT * FROM listings WHERE id = ?", [$item['listing_id']]); ?>
	    				    <div class='card bg-dark text-white border-white mt-4 p-2 mx-auto'>

	    					<div class="containter">
	    					    <div class='row'>
	    						<div class='col-4'>
	    						    <img src='/uploads/listing_images/<?= $listing['image_name']; ?>' class='rounded' style='max-width: 150px;'>
	    						</div>
	    						<div class="col-4 text-center">
	    						    <p class="h4 text-muted text-decoration-underline">
	    							<a href="/pages/listing.php?action=view&id=<?= $listing['id']; ?>">
									<?= '#' . htmlspecialchars($listing['title']); ?>
	    							</a>
	    						    </p>
	    						</div>

	    						<div class="col-4">

	    						</div>
	    					    </div>

	    					    <!-- price and remove goes here -->
	    					    <div class="row mt-4">

	    						<div class='col'>
	    						    <p class='h4 text-white py-2'>R<?= number_format($listing['price'], 2, '.', ''); ?></p>
	    						</div>

	    						<div class="col">
	    						    <div class="d-flex justify-content-center">
	    							<button class='btn btn-secondary text-white removeBtn' value='Remove' data-listing-id='<?= $listing['id']; ?>'>Remove</button>
	    						    </div>
	    						</div>
	    					    </div>
	    					</div>
	    				    </div>
					    <?php endforeach; ?>
					</div>
				    </div>


				    <div class="row mt-4" >
					<div class="col">
					    <p class="h3 text-white">Total Due: R<?= number_format($total, 2, '.', ''); ?></p>
					</div>

					<div class="col">
					    <?= $htmlForm; ?>
					</div>

				    </div>
				<?php endif; ?>
			    <?php else: ?>

    			    <p class="h2 text-white">A cart has not been opened. Add some listings to proceed.</p>

			    <?php endif; ?>
			</div>
		    </div>
		</div>
	    </div>
	</main>

	<?php include '../includes/html/footer.php'; ?>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>    
        <script src="../js/view_cart.js"></script>
    </body>

</body>
</html>
