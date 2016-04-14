<?php
/*
*
* Download album according with a url param.
*
*/
// Set these to be your secrets
$consumer_key      = 'OZZhUs9OMoN8YIYzaRtH0ytQJIri4ds2';
$consumer_secret   = '0f69057fe634415ef440b1d1067a69dd';
$user_token        = 'bf2242e08efa71275b2d4b7b771a5d89';
$user_token_secret = '92d166494dcfaf84c050fd94212c4b5aca92d07d13f75abb8d2423c15226c56b';

// This checks that you have OAuth installed
if (!class_exists('OAuth')) { 
	die("You need to install OAuth for this example to work. See http://php.net/manual/en/book.oauth.php for more information.\n");
}

// Define your request - this is a GET with ImageSizes expansion
$uri     = 'https://api.smugmug.com/api/v2/album/'.$_GET['album_key'].'!download';
$method  = 'PUT';
$params  = []; // you'll put post/patch values here
$headers = [
    'Accept' => 'application/json',	// set to 'text/xml' if you’d rather have an XML response
];

// Get your OAuth instance
$oauth = new OAuth($consumer_key, $consumer_secret); // documentation here http://php.net/manual/en/oauth.construct.php
$oauth->setToken($user_token, $user_token_secret); // documentation here http://php.net/manual/en/oauth.settoken.php

// Make the actual request
try {
	$oauth->fetch($uri, $params, $method, $headers); // documentation here http://php.net/manual/en/oauth.fetch.php
} catch (OAuthException $e) {
	echo 'Request failed: ' . $e->getMessage() . "\n"; 
	echo 'Response: ' . $oauth->getLastResponse() . "\n"; // documentation here http://php.net/manual/en/oauth.getlastresponse.php
	die();
}

/*
Also look at these resources for more interesting data about the request such as HTTP response code
	http://php.net/manual/en/oauth.getlastresponseheaders.php
	http://php.net/manual/en/oauth.getlastresponseinfo.php
*/

// Parse and display data
$jsonString = $oauth->getLastResponse();
$data = json_decode($jsonString, true);

//var_dump( $data['Response']['Download'][0]['WebUri'] );

$url = $data['Response']['Download'][0]['WebUri'];
echo 'URL:' . $url;
//
	if ( $url == null ){
		echo "You can't download this album, please try later."; // Error handling because Smugmug sometimes doesn't like having a lot of download requests
	} else {
		header("Location: $url");
		echo "<script>window.close();</script>";
	}

