<?php
include "cgi-bin/functions.php";
include "cgi-bin/config.php";

// CONFIG: Enable debug mode. This means we'll log requests into 'ipn.log' in the same directory.
// Especially useful if you encounter network errors or other intermittent problems with IPN (validation).
// Set this to 0 once you go live or don't require logging.
define ( "DEBUG", 0 );

// Set to 0 once you're ready to go live
define ( "USE_SANDBOX", 0 );

define ( "LOG_FILE", "./ipn.log" );

// Read POST data
// reading posted data directly from $_POST causes serialization
// issues with array data in POST. Reading raw POST data from input stream instead.
$raw_post_data = file_get_contents ( 'php://input' );
$raw_post_array = explode ( '&', $raw_post_data );
$myPost = array ();
foreach ( $raw_post_array as $keyval ) {
	$keyval = explode ( '=', $keyval );
	if (count ( $keyval ) == 2) {
		$myPost [$keyval [0]] = urldecode ( $keyval [1] );
	}
}
// read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-validate';
if (function_exists ( 'get_magic_quotes_gpc' )) {
	$get_magic_quotes_exists = true;
}
foreach ( $myPost as $key => $value ) {
	if ($get_magic_quotes_exists == true && get_magic_quotes_gpc () == 1) {
		$value = urlencode ( stripslashes ( $value ) );
	} else {
		$value = urlencode ( $value );
	}
	$req .= "&$key=$value";
}

// Post IPN data back to PayPal to validate the IPN data is genuine
// Without this step anyone can fake IPN data

if (USE_SANDBOX == true) {
	$paypal_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
} else {
	$paypal_url = "https://www.paypal.com/cgi-bin/webscr";
}

$ch = curl_init ( $paypal_url );
if ($ch == FALSE) {
	return FALSE;
}

curl_setopt ( $ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1 );
curl_setopt ( $ch, CURLOPT_POST, 1 );
curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
curl_setopt ( $ch, CURLOPT_POSTFIELDS, $req );
curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, 1 );
curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, 2 );
curl_setopt ( $ch, CURLOPT_FORBID_REUSE, 1 );

if (DEBUG == true) {
	curl_setopt ( $ch, CURLOPT_HEADER, 1 );
	curl_setopt ( $ch, CURLINFO_HEADER_OUT, 1 );
}

// CONFIG: Optional proxy configuration
// curl_setopt($ch, CURLOPT_PROXY, $proxy);
// curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
// Set TCP timeout to 30 seconds
curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 30 );
curl_setopt ( $ch, CURLOPT_HTTPHEADER, array (
		'Connection: Close'
) );

// CONFIG: Please download 'cacert.pem' from "http://curl.haxx.se/docs/caextract.html" and set the directory path
// of the certificate as shown below. Ensure the file is readable by the webserver.
// This is mandatory for some environments.
// $cert = __DIR__ . "./cacert.pem";
// curl_setopt($ch, CURLOPT_CAINFO, $cert);

$res = curl_exec ( $ch );
if (curl_errno ( $ch ) != 0) { // cURL error
	if (DEBUG == true) {
		error_log ( date ( '[Y-m-d H:i e] ' ) . "Can't connect to PayPal to validate IPN message: " . curl_error ( $ch ) . PHP_EOL, 3, LOG_FILE );
	}
	curl_close ( $ch );
	exit ();
} else {
	// Log the entire HTTP response if debug is switched on.
	if (DEBUG == true) {
		error_log ( date ( '[Y-m-d H:i e] ' ) . "HTTP request of validation request:" . curl_getinfo ( $ch, CURLINFO_HEADER_OUT ) . " for IPN payload: $req" . PHP_EOL, 3, LOG_FILE );
		error_log ( date ( '[Y-m-d H:i e] ' ) . "HTTP response of validation request: $res" . PHP_EOL, 3, LOG_FILE );

		// Split response headers and payload
		list ( $headers, $res ) = explode ( "\r\n\r\n", $res, 2 );
	}
	curl_close ( $ch );
}

// Inspect IPN validation result and act accordingly

if (strcmp ( $res, "VERIFIED" ) == 0) {
	// check whether the payment_status is Completed
	// check that txn_id has not been previously processed
	// check that receiver_email is your PayPal email
	// check that payment_amount/payment_currency are correct
	// process payment and mark item as paid.
	// assign posted variables to local variables

	$item_name = filter_input ( INPUT_POST, 'item_name', FILTER_SANITIZE_STRING );
	$payment_status = filter_input ( INPUT_POST, 'payment_status', FILTER_SANITIZE_STRING );
	$payment_amount = filter_input ( INPUT_POST, 'mc_gross', FILTER_SANITIZE_STRING );
	$payment_fee = filter_input ( INPUT_POST, 'payment_fee', FILTER_SANITIZE_STRING );
	$payment_currency = filter_input ( INPUT_POST, 'mc_currency', FILTER_SANITIZE_STRING );
	$txn_id = filter_input ( INPUT_POST, 'txn_id', FILTER_SANITIZE_STRING );
	$receiver_email = str_ireplace ( "%40", "@", strtolower ( filter_input ( INPUT_POST, 'receiver_email', FILTER_SANITIZE_STRING ) ) );
	$payer_email = str_ireplace ( "%40", "@", strtolower ( filter_input ( INPUT_POST, 'payer_email', FILTER_SANITIZE_STRING ) ) );
	$custom = filter_input ( INPUT_POST, 'custom', FILTER_SANITIZE_STRING ); // userId,clsId,busDirectoryId,adId,notUsed2
	$x = explode ( ",", $custom );
	$userId = $x [0];
	$clsId = $x [1];
	$busDirectoryId = $x [2];
	$adId = $x [3];
	$newpay2 = $x [4];

	if ($payment_status == "Completed" && $payment_currency == "USD" && $receiver_email == $myPaypalEmail) {
		$problem = 0;

		if ($item_name == 'ClassifiedTextLength') {
			$v = explode ( ".", ($payment_amount * 50) );
			$value = $v [0];
			$clsCheck = $db->prepare ( "SELECT userId FROM classifieds WHERE id=?" );
			$clsCheck->execute ( array (
					$clsId
			) );
			$clsCrow = $clsCheck->fetch ();
			$ui = $clsCrow ['userId'];
			if ($ui == $userId) {
				$clsUpdate = $db->prepare ( "UPDATE classifieds SET classifiedTextLength=? WHERE id=?" );
				$clsUpdate->execute ( array (
						$value,
						$clsId
				) );
			}
		}

		if ($item_name == 'BusinessListing') {
			addBusiListing ( $userId );
		}

		if ($item_name == 'AdvertisingTop') {
			$ad1 = $db->prepare ( "SELECT activeUntil FROM advertising WHERE id=?" );
			$ad1->execute ( array (
					$adId
			) );
			$ad1row = $ad1->fetch ();
			$oldTime = $ad1row ['activeUntil'];
			switch ($payment_amount) {
				case '144.00' :
					$tt = 15724800;
					break;
				case '240.00' :
					$tt = 31449600;
					break;
				case '400.00' :
					$tt = 62899200;
					break;
				default :
					$tt = 1;
					$problem = 1;
			}
			$newTime = ($time >= $oldTime) ? ($time + $tt) : ($oldTime + $tt);
			$ad2 = $db->prepare ( "UPDATE advertising SET activeUntil=? WHERE id=?" );
			$ad2->execute ( array (
					$newTime,
					$adId
			) );
			addBusiListing ( $userId );
		}

		if ($item_name == 'AdvertisingSide1') {
			$ad1 = $db->prepare ( "SELECT activeUntil FROM advertising WHERE id=?" );
			$ad1->execute ( array (
					$adId
			) );
			$ad1row = $ad1->fetch ();
			$oldTime = $ad1row ['activeUntil'];
			switch ($payment_amount) {
				case '108.00' :
					$tt = 15724800;
					break;
				case '180.00' :
					$tt = 31449600;
					break;
				case '300.00' :
					$tt = 62899200;
					break;
				default :
					$tt = 1;
					$problem = 1;
			}
			$newTime = ($time >= $oldTime) ? ($time + $tt) : ($oldTime + $tt);
			$ad2 = $db->prepare ( "UPDATE advertising SET activeUntil=? WHERE id=?" );
			$ad2->execute ( array (
					$newTime,
					$adId
			) );
			addBusiListing ( $userId );
		}

		if ($item_name == 'AdvertisingSide2') {
			$ad1 = $db->prepare ( "SELECT activeUntil FROM advertising WHERE id=?" );
			$ad1->execute ( array (
					$adId
			) );
			$ad1row = $ad1->fetch ();
			$oldTime = $ad1row ['activeUntil'];
			switch ($payment_amount) {
				case '72.00' :
					$tt = 15724800;
					break;
				case '120.00' :
					$tt = 31449600;
					break;
				case '200.00' :
					$tt = 62899200;
					break;
				default :
					$tt = 1;
					$problem = 1;
			}
			$newTime = ($time >= $oldTime) ? ($time + $tt) : ($oldTime + $tt);
			$ad2 = $db->prepare ( "UPDATE advertising SET activeUntil=? WHERE id=?" );
			$ad2->execute ( array (
					$newTime,
					$adId
			) );
			addBusiListing ( $userId );
		}

		if ($item_name == 'AdvertisingSide3') {
			$ad1 = $db->prepare ( "SELECT activeUntil FROM advertising WHERE id=?" );
			$ad1->execute ( array (
					$adId
			) );
			$ad1row = $ad1->fetch ();
			$oldTime = $ad1row ['activeUntil'];
			switch ($payment_amount) {
				case '36.00' :
					$tt = 15724800;
					break;
				case '60.00' :
					$tt = 31449600;
					break;
				case '100.00' :
					$tt = 62899200;
					break;
				default :
					$tt = 1;
					$problem = 1;
			}
			$newTime = ($time >= $oldTime) ? ($time + $tt) : ($oldTime + $tt);
			$ad2 = $db->prepare ( "UPDATE advertising SET activeUntil=? WHERE id=?" );
			$ad2->execute ( array (
					$newTime,
					$adId
			) );
			addBusiListing ( $userId );
		}
		function addBusiListing($userId) {
			$busiUpdate = $db->prepare ( "UPDATE users SET businessListing='1' WHERE id=?" );
			$busiUpdate->execute ( array (
					$userId
			) );
			$ba = $db->prepare ( "SELECT COUNT(*) FROM busiListing WHERE userId = ?" );
			$ba->execute ( array (
					$userId
			) );
			$barow = $ba->fetch ();
			if ($barow [0] == 0) {
				$busiAdd = $db->prepare ( "INSERT INTO busiListing VALUES(NULL,?,'','','','','','0','0','0','0','','','','0','0','0')" );
				$busiAdd->execute ( array (
						$userId
				) );
			}
		}

		$checkdup = $db->prepare ( "SELECT COUNT(*) FROM payments WHERE txnId=?" );
		$checkdup->execute ( array (
				$txn_id
		) );
		$cdrow = $checkdup->fetch ();
		$d = ($cdrow [0] == '1') ? '1' : '0';
		$pay = $db->prepare ( "INSERT INTO payments VALUES(NULL,?,?,?,?,?,?,?,?,?,?,?,?,'0')" );
		$pay->execute ( array (
				$txn_id,
				$item_name,
				$payment_amount,
				$payment_fee,
				$userId,
				$clsId,
				$busDirectoryId,
				$adId,
				$newpay2,
				$d,
				$payer_email,
				$problem
		) );
	}

	if (DEBUG == true) {
		error_log ( date ( '[Y-m-d H:i e] ' ) . "Verified IPN: $req " . PHP_EOL, 3, LOG_FILE );
	}
} else if (strcmp ( $res, "INVALID" ) == 0) {
	// log for manual investigation
	// Add business logic here which deals with invalid IPN messages
	if (DEBUG == true) {
		error_log ( date ( '[Y-m-d H:i e] ' ) . "Invalid IPN: $req" . PHP_EOL, 3, LOG_FILE );
	}
}