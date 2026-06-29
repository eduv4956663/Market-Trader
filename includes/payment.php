<?php

class merchant {

    public static string $merchant_id = '10049767';
    public static string $merchant_key = '175e72k4f6om4';
    public static string $passphrase = 'tradertest67';
}

// payment verification code adapted from Payfast documentation
// Available: https://developers.payfast.co.za/docs#home
class payment {

    /**
     * @param array $data
     * @param null $passPhrase
     * @return string
     */
    public static function generateSignature($data, $passPhrase = null) {
	// Create parameter string
	$pfOutput = '';
	foreach ($data as $key => $val) {
	    if ($val !== '') {
		$pfOutput .= $key . '=' . urlencode(trim($val)) . '&';
	    }
	}
	// Remove last ampersand
	$getString = substr($pfOutput, 0, -1);
	if ($passPhrase !== null) {
	    $getString .= '&passphrase=' . urlencode(trim($passPhrase));
	}
	return md5($getString);
    }

    public static function pfValidSignature($pfData, $pfParamString, $pfPassphrase = null) {
// Calculate security signature
	if ($pfPassphrase === null) {
	    $tempParamString = $pfParamString;
	} else {
	    $tempParamString = $pfParamString . '&passphrase=' . urlencode($pfPassphrase);
	}

	$signature = md5($tempParamString);
	return ($pfData['signature'] === $signature);
    }

    public static function pfValidIP() {
// Variable initialization
	$validHosts = array(
	    'www.payfast.co.za',
	    'sandbox.payfast.co.za',
	    'w1w.payfast.co.za',
	    'w2w.payfast.co.za',
	);

	$validIps = [];

	foreach ($validHosts as $pfHostname) {
	    $ips = gethostbynamel($pfHostname);

	    if ($ips !== false) {
		$validIps = array_merge($validIps, $ips);
	    }
	}

// Remove duplicates
	$validIps = array_unique($validIps);
	$referrerIp = gethostbyname(parse_url($_SERVER['HTTP_REFERER'])['host']);
	if (in_array($referrerIp, $validIps, true)) {
	    return true;
	}
	return false;
    }

    public static function pfValidPaymentData($cartTotal, $pfData) {
	return !(abs((float) $cartTotal - (float) $pfData['amount_gross']) > 0.01);
    }

    public static function pfValidServerConfirmation($pfParamString, $pfHost = 'sandbox.payfast.co.za', $pfProxy = null) {
	// Use cURL (if available)
	if (in_array('curl', get_loaded_extensions(), true)) {
	    // Variable initialization
	    $url = 'https://' . $pfHost . '/eng/query/validate';

	    // Create default cURL object
	    $ch = curl_init();

	    // Set cURL options - Use curl_setopt for greater PHP compatibility
	    // Base settings
	    curl_setopt($ch, CURLOPT_USERAGENT, NULL);  // Set user agent
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);      // Return output as string rather than outputting it
	    curl_setopt($ch, CURLOPT_HEADER, false);      // Don't include header in output
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

	    // Standard settings
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_POST, true);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $pfParamString);
	    if (!empty($pfProxy)) {
		curl_setopt($ch, CURLOPT_PROXY, $pfProxy);
	    }

	    // Execute cURL
	    $response = curl_exec($ch);
	    curl_close($ch);
	    if ($response === 'VALID') {
		return true;
	    }
	}
	return false;
    }
}
