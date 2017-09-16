<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

require __DIR__ . '/../vendor/autoload.php';


//$rawData = $request->getContent(); // This is how you would retrieve this with Laravel or Symfony 2.
//$request = new \APIAI\Request\Request('test');
//$request = $request->fromData();

//var_dump($request);exit;

$response = new \APIAI\Response\Response('pioupiou');
$response->respond('Cooool. I\'ll lower the temperature a bit for you!')
	->withDisplayText('Temperature decreased by 2 degrees')
	->withCard('My card title','My formatted text');


header('Content-Type: application/json');
echo json_encode($response->render());
exit;