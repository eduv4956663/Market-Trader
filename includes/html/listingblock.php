<?php if (empty($listings)): ?>
    <h4>No listings found.</h4>

<?php else: ?>
    <div class='container' >
        <div class='row g-4'>
	    <?php foreach ($listings as $listing): ?>
		<div class='col-md-5 px-4'>
		    <div class='row align-items-center border border-secondary rounded mb-3 p-4'>

			<!-- title row -->
			<div class='row text-center'>
			    <div class='col-12'>
				<h5 class='mb-2'>
				    <a href="/pages/listing.php?action=view&id=<?= htmlspecialchars($listing['id']); ?>"><?= htmlspecialchars($listing['title']); ?></a>
				</h5>
			    </div>
			</div>

			<!-- image -->
			<div class='col-auto'>
			    <img src="<?= '/uploads/listing_images/' . htmlspecialchars($listing['image_name']); ?>"
				 alt="listing pic"
				 class='rounded'
				 style="width: 140px; object-fit: cover;"
				 >
			</div>

			<!-- text area -->
			<div class='col'>
			    <!-- content cols -->
			    <div class='row'>
				<div class='col-5'>
				    <p class='mb-0 text-light small'><?= "#" . htmlspecialchars($listing['id']); ?></p>
				    <p class='mb-0 text-light small'><?= "R" . htmlspecialchars($listing['price']); ?></p>
				    <p class='mb-0 text-light small'><?= "Views: " . htmlspecialchars($listing['views']); ?></p>
				</div>
				<div class='col-7'>
				    <p class='mb-0 text-light small'><?= "Favourites: " . htmlspecialchars($listing['favourites']); ?></p>
				    <p class='mb-0 text-light small'><?= "Status: " . htmlspecialchars($listing['status']); ?></p>
				    <p class='mb-0 text-light small'><?= "Created: " . htmlspecialchars($listing['created_at']); ?></p>
				</div>
			    </div>

			</div>
		    </div>
		</div>

	    <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>