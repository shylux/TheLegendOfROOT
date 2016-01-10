<?php  

// http://stackoverflow.com/questions/5645412/parsing-get-request-parameters-in-a-url-that-contains-another-url
// restore the encoded get datas
$get_parameters = array();
if (isset($_SERVER['QUERY_STRING'])) {
  $pairs = explode('&', urldecode($_SERVER['QUERY_STRING']));
  foreach($pairs as $pair) {
    $part = explode('=', $pair);
    $_GET[$part[0]] = sizeof($part)>1 ? $part[1] : "";
    }
 } 

if (session_status() == PHP_SESSION_NONE) {
    session_start(); 
}
 
function saveCoordinates() { 
	$data = $_GET['d'];
	echo $data;
	$coordinatesData = json_decode($data, true); 
	$_SESSION['chat']['position'] = $coordinatesData; 
} 

if ( !isset($_GET['f']) ) {
	return;
}

if ( strlen( $_GET['f'] ) < 3 && strlen( $_GET['f'] ) > 1 ) { 
	if ( $_GET['f'] == 'sc' ) saveCoordinates();  
}

?>
