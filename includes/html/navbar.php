<nav class="navbar navbar-expand-lg navbar-light bg-dark border-top border-bottom flex-row">
    <?php if ($filename !== 'index'): ?>
        <li class='nav-item'>
    	<a href="/index.php" class='nav-link link-light mx-4'>Home</a>
        </li>
    <?php endif; ?>

    <?php if ($filename !== 'profile'): ?>
        <li class='nav-item'>
    	<a href="/pages/profile.php?id=self" class='nav-link link-light mx-4'>Profile</a>
        </li>
    <?php endif; ?>

    <?php if ($filename !== 'view_cart'): ?>
        <li class='nav-item'>
    	<a href="/pages/view_cart.php" class='nav-link link-light mx-4'>Cart</a>
        </li>
    <?php endif; ?>

    <li class='nav-item dropdown'>

	<a class='nav-link dropdown-toggle link-light mx-4' href='#' role='button' data-bs-toggle='dropdown'>
	    Listings
	</a>

	<ul class='dropdown-menu'>
	    <?php if ($filename !== 'browse'): ?>
    	    <li>
    		<a class='dropdown-item' href='/pages/browse.php'>Browse</a>
    	    </li>
	    <?php endif; ?>

	    <?php if ($filename !== 'listing'): ?>
    	    <li>
    		<a class='dropdown-item' href='/pages/listing.php?action=create'>Create Listing</a>
    	    </li>
	    <?php endif; ?>

	</ul>
    </li>
</nav>
