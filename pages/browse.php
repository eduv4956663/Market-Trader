<?php
require_once '../includes/db.php';
require_once '../includes/helpers.php';
require_once '../includes/user.php';

if (!user::has_session()) {
    response::redirect('../pages/login.php');
}
$filename = 'browse';

$search_term = '%' . helper::get_str('search-request') . '%';
$search_cat = helper::get_str('category') ?? 'Any';
$page = helper::get_int('page') ?? 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

$categories = database::fetchall("SELECT * FROM product_cats WHERE ?", ['1=1']);
$listings = null;

if ($search_cat !== 'Any') {
    $listings = database::fetchall("SELECT * FROM listings WHERE status = ? AND category = ? AND title LIKE ? LIMIT ? OFFSET ?", ['active', $search_cat, $search_term, $per_page, $offset]);
} else {
    $listings = database::fetchall("SELECT * FROM listings WHERE status = ? AND title LIKE ? LIMIT ? OFFSET ?", ['active', $search_term, $per_page, $offset]);
}

$total = database::fetch("SELECT COUNT(*) FROM listings WHERE status = ?", ['active']);
$total_pages = ceil($total['COUNT(*)'] / $per_page);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" content='width=device-width, initial-scale=1'>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
        <title>Browse Listings</title>
    </head>
    <body class='bg-dark text-white d-flex flex-column min-vh-100'>

	<?php include '../includes/html/header.php'; ?>
	<?php include '../includes/html/navbar.php'; ?>

	<div class='container'>

	    <div class='row py-4'>
		<div class='col'>
		    <div class='card mx-auto p-4' style='max-width: 540px;'>
			<form method="get" class='d-flex gap-2'>
			    <select name='category' class='form-label'>
				<option value='Any'>Any</option>
				<?php foreach ($categories as $category): ?>
				    <?= "<option value='" . $category['name'] . "'>" . $category['name'] . "</option>"; ?>
				<?php endforeach; ?>
			    </select>
			    <input type="text" placeholder="Search for listings" name="search-request" class='form-control'>
			    <input type="submit" class='btn btn-dark w-25'>
			</form>
		    </div>
		</div>
	    </div>


	    <div class='row'>
		<div class='col'>
		    <?php if (empty($listings)): ?>
    		    <h3>No listings found.</h3>
		    <?php else: ?>
			<?php include '../includes/html/listingblock.php'; ?>
		    <?php endif; ?>
		</div>
	    </div>


	    <div class='row py-4'>
		<div class='col'>
		    <nav>
			<ul class="pagination justify-content-center align-items-center">

			    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
				<a class="page-link" href="?page=<?= $page - 1 ?>">&#8249;</a>
			    </li>

			    <li class="page-item disabled">
				<span class="page-link border-0 bg-transparent"><?= $page ?> / <?= $total_pages ?></span>
			    </li>

			    <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
				<a class="page-link" href="?page=<?= $page + 1 ?>">&#8250;</a>
			    </li>

			</ul>
		    </nav>
		</div>
	    </div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>        <footer>

	</footer> 
    </body>
</html>
