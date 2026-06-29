<footer class="bg-dark text-white text-center py-4">
    <div class="container">
        <p class="mb-2 text-white">&copy; 2026 Company. All rights reserved.</p>

        <ul class='list-inline mt-2'>

	    <?php if ($filename !== 'about'): ?>
    	    <li class='list-inline-item'>
    		<a href="/Testproj/pages/about.php" class="link-primary link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">About</a>
    	    </li>
	    <?php endif; ?>

	    <?php if ($filename !== 'index'): ?>
    	    <li class='list-inline-item'>
    		<a href="/Testproj/index.php" class="link-primary link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">About</a>
    	    </li>
	    <?php endif; ?>

            <li class='list-inline-item'>
                <a href="/Testproj/pages/admin/admin_login.php" class="link-primary link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">Admin Dashboard</a>
            </li>

        </ul>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</footer>
