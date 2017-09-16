<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

require __DIR__ . '/../vendor/autoload.php';

try {
		
	$rawData = file_get_contents("php://input");
	$post = json_decode($rawData, true);
	//file_put_contents("post.log",print_r($post,true));
	$station_id = $result['result']['parameters']['station_id'];

	$curl = new Curl\Curl();
	$curl->get('http://api.pioupiou.fr/v1/live/'.$station_id);

	if ($curl->error) {
	    //$curl->error_code;
	    $message = 'Erreur de communication avec Pioupiou';
	}
	else {
	    $response = json_decode($curl->response, true);

	    $measurements = $response['data']["measurements"];
	    $message = $response['data']['meta']['name']." vent moyen ".$measurements['wind_speed_avg'].' kilomètre heure';
	}

} catch (TypeError $e) {

	$message = $e->getMessage();
}

$response = new \APIAI\Response\Response('pioupiou');
$response->respond($message)
	->withDisplayText($message);

header('Content-Type: application/json');
echo json_encode($response->render());
exit;