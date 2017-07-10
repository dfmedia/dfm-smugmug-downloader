<?php
/*
*
* This is a example of a Smugmug call getting an album lists with a download button.
* @author David Leal <dleal@medianewsgroup.com>
*
*/
// Set these to be your secrets
$consumer_key      = '';
$consumer_secret   = '';
$user_token        = '';
$user_token_secret = '';

// This checks that you have OAuth installed
if (!class_exists('OAuth')) { 
	die("You need to install OAuth for this example to work. See http://php.net/manual/en/book.oauth.php for more information.\n");
}

//Pagination
if ( isset( $_GET['page'] ) && $_GET['page'] != 0  ){
	$limit = 100;
	$count = "?start=" . $_GET['page'] * $limit . "&count=100";
} else {
	$count = "?start=1&count=100";
}
// Define your request - this is a GET with ImageSizes expansion
$uri     = 'https://api.smugmug.com/api/v2/user/ParkRecordPhoto!albums' . $count;
$method  = 'GET';
$params  = array(); // you'll put post/patch values here
$headers = array(
    'Accept' => 'application/json',	// set to 'text/xml' if you’d rather have an XML response
);

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>Smugmug Albums Download Helper</title>
	<!-- Bootstrap -->
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
	<!-- Theme -->
	<link rel="stylesheet" href="https://bootswatch.com/cerulean/bootstrap.min.css">

	<style>
	body{margin-left: 50px;}
	</style>
</head>
<body>
<div class="container">
	<div class="page-header">
		<h1>Albums from Smugmug</h1>
		<p>This is a simple tool to list albums from Park Record and with the functionality of download full albums in .zip files. Note: Some times Smugmug doesn't like when you download several albums in a short period of time, so it will be good to download 1 by 1 and wait one minute between downloads. If you want to go by the traditional way, just go to the gallery URL and click download full gallery.
	</div>
	<div class="bs-component">
              <table class="table table-striped table-hover ">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>URL</th>
                    <th>Details</th>
                    <th>Last update</th>
                    <th>Download</th>
                  </tr>
                </thead>
                <tbody>
        <?php
			// Printing albums
			foreach ($data['Response']['Album'] as $key => $album) {
				if ( isset( $_GET['page'] ) && $_GET['page'] != 0 ){
					$count = 100 * $_GET['page'];
					$key = $key + $count;
				}
		?>
                  <tr>
                  	<td><?php echo $key ?></td>
                    <td><?php echo $album['Name'] ?></td>
                    <td><a href="<?php echo $album['WebUri'] ?>"><?php echo $album['WebUri'] ?></a></td>
                    <td><?php echo "Size: " . $album['TotalSizes'] . " - Images: " . $album['ImageCount'] ?></td>
                    <td><?php echo $album['ImagesLastUpdated'] ?></td>
                    <td><a href="download.php?album_key=<?php echo $album['AlbumKey']?>" target="_blank" class="btn btn-primary">Download</a></td>
                  </tr>
		<?php
			} // Foreach Ends
		?>
                </tbody>
              </table> 
		
		<ul class="pagination">
			<?php
			// Pagination
			$pages = floor( $data['Response']['Pages']['Total'] / 100 );
			for ( $x = 0; $x <= $pages; $x++ ){
				if ( isset( $_GET['page'] ) && $x == $_GET['page'] ){
					echo '<li class="active"><a href="?page='.$x.'">'.$x.'</a></li>';
				} else {
					echo '<li><a href="?page='.$x.'">'.$x.'</a></li>';
				}
			}
			?>
		</ul>
		<?php echo "Total result:" . $data['Response']['Pages']['Total'] ?>
<script src="http://code.jquery.com/jquery.js"></script>
<script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
</div>
</body>
</html>