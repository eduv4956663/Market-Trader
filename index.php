<?php
require_once 'includes/helpers.php';
require_once 'includes/user.php';
require_once 'includes/db.php';
session_start();

if ($has_session = user::has_session() !== true) {
    response::redirect('pages/login.php');
}

$filename = 'index';

$listings = database::fetchall("SELECT * FROM listings WHERE status = ? ORDER BY created_at DESC", ['active']);
$categories = database::fetchall("SELECT * FROM product_cats WHERE ?", [1]);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">        <link href='style.css' rel="stylesheet">
        <title>Home</title>
    </head>
    <body class='bg-dark text-light d-flex flex-column min-vh-100'>
	<?php include 'includes/html/header.php'; ?>
	<?php include 'includes/html/navbar.php'; ?>

	<div class="container flex-grow-1 py-4">
	    <div class="row">
		<div class="col">
		    <h3 class='h3 text-decoration-underline'>Recent Active Listings</h3>
		</div>
	    </div>
	    <div class="row">
		<div class="col">
		    <div id="home-listings">
			<?php include 'includes/html/listingblock.php'; ?>
		    </div>
		</div>
	    </div>
	</div>

	<?php include 'includes/html/footer.php'; ?>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    </body>
</html>
