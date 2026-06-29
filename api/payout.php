<?php

require_once '../includes/db.php';
require_once '../includes/helpers.php';

http_response_code(200);

$pfData = $_POST;
$pfParamString = "";
foreach ($pfData as $key => $val) {
    $pfData[$key] = stripslashes($val);
}

foreach ($pfData as $key => $val) {
    if ($key !== 'signature') {
	$pfParamString .= $key . '=' . urlencode($val) . '&';
    } else {
	break;
    }
}

$pfParamString = substr($pfParamString, 0, -1);

function pfValidSignature($pfData, $pfParamString, $pfPassphrase = 'tradertest67') {
    if ($pfPassphrase === null) {
	$tempParamString = $pfParamString;
    } else {
	$tempParamString = $pfParamString . '&passphrase=' . urlencode($pfPassphrase);
    }

    $signature = md5($tempParamString);
    return ($pfData['signature'] === $signature);
}

$check = pfValidSignature($pfData, $pfParamString, 'tradertest67');
$cart_id = (int) helper::fetch_post("m_payment_id");

if (!$check) {
    database::execute_arr("UPDATE carts SET status = ? WHERE id = ?", ['fraudulent', $cart_id]);
    exit;
}

$payment_status = helper::fetch_post("payment_status");

if ($payment_status === 'COMPLETE') {
    $gross_amt = (float) helper::fetch_post("amount_gross");
    $net_amt = (float) helper::fetch_post("amount_net");

    $cart = database::fetch("SELECT * FROM carts WHERE id = ?", [$cart_id]);
    $cart_items = database::fetchall("SELECT * FROM cart_items WHERE cart_id = ?", [$cart_id]);

    $buyer = database::fetch("SELECT * FROM users WHERE id = ?", [$cart['user_id']]);

    database::execute_arr("UPDATE carts SET status = ? WHERE id = ?", ['closed', $cart['id']]);
    database::execute_arr("INSERT INTO orders (owner_id, cart_id, price, item_count) VALUES (?, ?, ?, ?)", [$buyer['id'], $cart['id'], $gross_amt, count($cart_items)]);

    $total_seller_payout = 0.0;
    foreach ($cart_items as $item) {
	$listing = database::fetch("SELECT * FROM listings WHERE id = ?", [$item['listing_id']]);
	$owner = database::fetch("SELECT * FROM users WHERE id = ?", [$listing['owner_id']]);

	$amt_owed = (float) $listing['price'] * 0.90;
	$total_seller_payout += $amt_owed;

	// seller credit
	database::execute_arr("UPDATE users SET wallet_balance = wallet_balance + ? WHERE id = ?", [$amt_owed, $owner['id']]);

	// set listing to sold
	database::execute_arr("UPDATE listings SET status = ? WHERE id = ?", ['sold', $item['listing_id']]);
    }

    // update ledger for display
    $profit = $net_amt - $total_seller_payout;
    database::execute_arr("INSERT INTO platform_ledger (amount) VALUES (?)", [$profit]);
}
