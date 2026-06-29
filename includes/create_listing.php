<?php

if (!user::has_session()) {
    response::redirect('../pages/login.php');
}

$categories = database::fetchall("SELECT * FROM product_cats WHERE ?", ['1=1']);

if(helper::fetch_global($_SERVER, 'REQUEST_METHOD') === 'POST') {
        
    $listing_name = helper::fetch_global($_POST, 'listing-name');
    $description = helper::fetch_global($_POST, 'description');
    $category = helper::fetch_global($_POST, 'category');
    $image = $_FILES['cover-img'] ?? null;
    $location = helper::fetch_global($_POST, 'location');
    $price = (double)helper::fetch_global($_POST, 'price');
    
    
    
    
    if ($image['error'] !== UPLOAD_ERR_OK) {
        die('Upload failed.');
    }
    
    $img_info = img_helper::process_img($image);
    
    
    if ($img_info['valid'] === false) {
        die('Inavalid image type');
    }
    
    $dest = '../uploads/listing_images/' . $img_info['new_name'];
    
    if (!move_uploaded_file($image['tmp_name'], $dest)) {
        die('Failed to move upload.');
    }
    
    $response = database::execute_arr(
        "INSERT INTO listings (owner_id, title, description, price, image_name, location, category) VALUES (?, ?, ?, ?, ?, ?, ?)",
        [user::get_user_id(), $listing_name, $description, $price, $img_info['new_name'], $location, $category]
    );
    
    if ($response === false) {
        die('Post Failed.');
    }
    
    response::redirect('../index.php');
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" content='width=device-width, initial-scale=1'>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">        <title><?= $title ?></title>
    </head>
    <body class='bg-dark text-light d-flex flex-column min-vh-100'>
        <?php include 'html/header.php'; ?>
        <?php include 'html/navbar.php'; ?>
        
	<main class="flex-grow-1 p-3">
	
        <div class='card mx-auto p-4' style='max-width: 540px;'>
            
            <h3 class='h3 text-center'>Create New Listing</h3><br>
            
            <form method='post' enctype='multipart/form-data'>
                
                <div class='mb3'>
                    <h3 class='h3'>Listing Details:</h3>
                </div>
                
                <div class='mb-3'>
                    <input type='text' name='listing-name' placeholder='Listing Name' class='form-control my-2'>                    
                    <input type='text' name='description' placeholder ='Description' class='form-control my-2'>
                    
                    <input type='text' name='location' placeholder='Address' class='form-control my-2'>
                    <input type='number' name='price' placeholder='Price (Rand)' class='form-control my-2'>
                    
                    <label for="cat" class='form-label mt-2'>Listing Category:</label>
                    <select name="category" id="cat" class='form-select'>
                        <?php foreach ($categories as $category): ?>
                            <?= "<option value='" . $category['name'] . "'>" . $category['name'] . "</option>" ?>
                        <?php endforeach; ?>
                    </select>
                    
                    <!-- make image preview -->
                    
                    <label class='btn btn-outline-secondary w-100 mt-4'>Upload Cover Image
                        <input type='file' name='cover-img' accept='.png, .jpg, .webp' hidden>
                    </label>

                    <input type="submit" class='btn btn-dark w-100 my-2'>

                </div>
                

                
            </form>
        </div>
        </main>
        <?php include 'html/footer.php'; ?>
        
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script
    </body>
</html>