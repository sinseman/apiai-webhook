<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

require __DIR__ . '/../vendor/autoload.php';

try {
		
	$rawData = file_get_contents("php://input");
	$post = json_decode($rawData, true);
	//file_put_contents("post.log",print_r($post,true));
	$station_id = $post['result']['parameters']['station_id'];

	$curl = new Curl\Curl();
	$curl->get('http://api.pioupiou.fr/v1/live/'.$station_id);

	if ($curl->error) {
	    //$curl->error_code;
	    $message = 'Erreur de communication avec Pioupiou';
	}
	else {
	    $response = json_decode($curl->response, true);

	    $measurements = $response['data']["measurements"];
	    $wind_heading = $measurements['wind_heading'];

		$k = ceil(($wind_heading - 11.25)/22.5);
		if ($k > 15) {
			$k = 0;
		}

		$directions = array(
			'Nord',
			'Nord-Nord-Est',
			'Nord-Est',
			'Est-Nord-Est',
			'Est',
			'Est-Sud-Est',
			'Sud-Est',
			'Sud-Sud-Est',
			'Sud',
			'Sud-Sud-Ouest',
			'Sud-Ouest',
			'Ouest-Sud-Ouest',
			'Ouest',
			'Ouest-Nord-Ouest',
			'Nord-Ouest',
			'Nord-Nord-Ouest',
		);

		$direction = $directions[$k];

	    $message = $response['data']['meta']['name'].' vent moyen '.$direction.' '.round($measurements['wind_speed_avg']).' kilomètre heure, maxi '.round($measurements['wind_speed_max']).', mini '.round($measurements['wind_speed_min']);
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