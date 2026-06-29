<?php

require_once '../includes/db.php';
require_once '../includes/helpers.php';
require_once '../includes/user.php';
if (!user::has_session()) {
    response::echo_json([
	'response' => 'Error occurred',
	'error' => 'Not logged in'
    ]);
}
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($data === null) {
	response::echo_json([
	    'response' => 'Error occurred',
	    'error' => 'Could not parse request'
	]);
    }

    $action = $data['action'];
    $listing_id = $data['listing_id'];
    $user_id = user::get_user_id();

    $listing = database::fetch("SELECT * FROM listings WHERE id = ?", [$listing_id]);
    $user_cart = database::fetch("SELECT * FROM carts WHERE user_id = ? AND status = ?", [$user_id, 'open']);

    if ($listing === false) {
	response::echo_json([
	    'response' => 'Error occurred.',
	    'error' => "No listing found."
	]);
    }

    if ($listing['owner_id'] === $user_id) {
	response::echo_json([
	    'response' => 'Error occurred.',
	    'error' => 'Cannot add a listing you own to cart.'
	]);
    }

    if ($action === 'add') {
	if ($user_cart === false) {
	    database::execute_arr("INSERT INTO carts (user_id) VALUES (?)", [$user_id]);
	    $user_cart = database::fetch("SELECT * FROM carts WHERE user_id = ? AND status = ?", [$user_id, 'open']);

	    if ($user_cart === false) {
		response::echo_json([
		    'response' => 'Error occurred.',
		    'error' => 'Failed to fetch cart.'
		]);
	    }
	}

	$cart_item = database::fetch("SELECT * FROM cart_items WHERE listing_id = ?", [$listing_id]);

	if ($cart_item && ($cart_item['cart_id'] !== $user_cart['id'])) {

	    if ($listing['status'] !== 'reserved') {
		database::execute_arr("UPDATE listings SET status = ? WHERE id = ?", ['reserved', $listing['id']]);
	    }

	    response::echo_json([
		'response' => 'Error occurred',
		'error' => 'Listing is reserved.'
	    ]);
	}

	if ($cart_item === false) {

	    if ($listing['status'] !== 'active') {
		response::echo_json([
		    'response' => 'Error occurred.',
		    'error' => "Listing $listing_id is not active."
		]);
	    }

	    database::execute_arr("INSERT INTO cart_items (listing_id, cart_id) VALUES (?, ?)", [$listing_id, $user_cart['id']]);
	    database::execute_arr("UPDATE listings SET status = ? WHERE id = ?", ['reserved', $listing_id]);

	    response::echo_json([
		'response' => "Added listing #$listing_id to cart",
		'error' => 'None'
	    ]);
	}

	if ($listing['status'] !== 'reserved') {
	    database::execute_arr("UPDATE listings SET status = ? WHERE id = ?", ['reserved', $listing_id]);
	}

	response::echo_json([
	    'response' => "Listing #$listing_id already in cart.",
	    'error' => 'None'
	]);
    }

    if ($action === 'remove') {
	// does user have cart
	if ($user_cart === false) {
	    response::echo_json([
		'response' => 'Error occurred.',
		'error' => 'No cart found.'
	    ]);
	}

	// yes, get cart
	$cart_item = database::fetch("SELECT * FROM cart_items WHERE listing_id = ? AND cart_id = ?", [$listing_id, $user_cart['id']]);

	// item in cart? No
	if ($cart_item === false) {
	    response::echo_json([
		'response' => "Cart does not have listing #$listing_id" . ".",
		'error' => 'None.'
	    ]);
	}

	// yes, remove
	database::execute_arr("DELETE FROM cart_items WHERE listing_id = ?", [$listing_id]);

	$listing = database::fetch("SELECT * FROM listings WHERE id = ?", [$listing_id]);

	if ($listing === false) {
	    response::echo_json([
		'response' => 'Error occurred.',
		'error' => 'Could not fetch listing for update'
	    ]);
	}

	if ($listing['status'] === 'reserved') {
	    database::execute_arr("UPDATE listings SET status = ? WHERE id = ?", ['active', $listing_id]);
	}

	response::echo_json([
	    'response' => "Successfully removed listing #$listing_id from cart",
	    'error' => 'None.'
	]);
    }

    response::echo_json([
	'response' => 'Error occurred.',
	'error' => 'Invalid action provided.'
    ]);
}

response::echo_json([
    'response' => 'Error occurred',
    'error' => 'Expected POST, received GET request.'
]);
