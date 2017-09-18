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
	    $wind_heading = $measurements['wind_heading'];

		$degrees = array(
			348,75,
			11,25,
			33,75,
			56,25,
			78,75,
			101,25,
			123,75,
			146,25,
			168,75,
			191,25,
			213,75,
			236,25,
			258,75,
			281,25,
			303,75,
			326,25,
		);

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

		foreach ($degrees as $k => $degree) {
			$nextDegree = (isset($degree[$k + 1]))? $degree[$k + 1]: $degree[0];

			if ($wind_heading >= $degree && $wind_heading < $nextDegree){
				break;
			}
		}

	    $message = $response['data']['meta']['name']." vent moyen ".$directions[$k].' '.$measurements['wind_speed_avg'].' kilomètre heure';
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