<? 
session_name("OAuth");
session_start();

require_once('config.php');
require_once('tmhOAuth.php');

if (isset($_REQUEST['id'])) {
	$_SESSION['id'] = $_REQUEST['id'];
}

if (!isset($_REQUEST['oauth_verifier'])) {

	// Step 1: Request a temporary token and
	// Step 2: Direct the user to the authorize web page

	$tmhOAuth = new tmhOAuth(array(
		'consumer_key' => CONSUMER_KEY,
		'consumer_secret' => CONSUMER_SECRET,
	));
	
	$callback_url = "http".(!empty($_SERVER['HTTPS'])?"s":"")."://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
	
	$code = $tmhOAuth->apponly_request(array(
	'without_bearer' => true,
	'method' => 'POST',
	'url' => $tmhOAuth->url('oauth/request_token', ''),
	'params' => array(
		'oauth_callback' => $callback_url,
	),
	));
	
  if ($code != 200) {
    error("There was an error communicating with Twitter. {$tmhOAuth->response['response']}");
    return;
  }

  // store the params into the session so they are there when we come back after the redirect
  $_SESSION['oauth'] = $tmhOAuth->extract_params($tmhOAuth->response['response']);

  // check the callback has been confirmed
  if ($_SESSION['oauth']['oauth_callback_confirmed'] !== 'true') {
    error('The callback was not confirmed by Twitter so we cannot continue.');
  } else {
    $url = $tmhOAuth->url('oauth/authorize', '') . "?oauth_token={$_SESSION['oauth']['oauth_token']}";
	header("Location: " . $url);
	exit;
  }
  
} else {

	// Step 3: This is the code that runs when Twitter redirects the user to the callback. Exchange the temporary token for a permanent access token

	$tmhOAuth = new tmhOAuth(array(
		'consumer_key' => CONSUMER_KEY,
		'consumer_secret' => CONSUMER_SECRET,
		'user_token' => $_SESSION['oauth']['oauth_token'],
		'user_secret' => $_SESSION['oauth']['oauth_token_secret'],
	));

	$code = $tmhOAuth->user_request(array(
		'method' => 'POST',
		'url' => $tmhOAuth->url('oauth/access_token', ''),
		'params' => array(
			'oauth_verifier' => trim($_REQUEST['oauth_verifier']),
		)
	));

	if ($code == 200) {
		$oauth_creds = $tmhOAuth->extract_params($tmhOAuth->response['response']);
		header( "refresh:3; url=admin.php" ); 
		echo "Account <b>".$oauth_creds['screen_name']."</b> verified!";
		$oauth_token = $oauth_creds['oauth_token'];
		$oauth_token_secret = $oauth_creds['oauth_token_secret'];
		$id = $_SESSION['id'];
		if (!($upd_token = $mysqli->prepare("UPDATE destination SET oauth_token = ? , oauth_token_secret = ? WHERE id = ?"))) {
			die("Prepare an SQL statement error: (" . $mysqli->errno . ") " . $mysqli->error);
		}
		if (!$upd_token->bind_param("ssi", $oauth_token, $oauth_token_secret, $id)) {
			die("Error binding parameters: (" . $upd_token->errno . ") " . $upd_token->error);
		}
		if (!$upd_token->execute()) {
			echo("Token update error: (" . $upd_token->errno . ") " . $upd_token->error);
		}
		$mysqli->close;
	} else {
		echo "Verification error!";
	}

}
?>

<br><br>
<a href=admin.php>Return to admin panel</a>

<?
/* Clear session */
session_destroy();
?>
