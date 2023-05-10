<?php

use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

$zipcode = session('zipcode', '45056');
// retrieve the weather data from cache if it exists
if (Cache::has($zipcode)) {
    $weatherData = Cache::get($zipcode);
} else {
    try {
        // if data is not in cache, call the Lambda function using Guzzle
        $client = new Client();
        $response = $client->request('GET', 'https://iy3sdcud2g.execute-api.us-east-1.amazonaws.com/default/weather', [
            'query' => [
                'zipcode' => $zipcode
	    ],
            'headers' => [
	        'X-AUTH-FINAL-TOKEN' => session('authKey', 'fail')
	    ]

        ]);

        // check the response status code
        if ($response->getStatusCode() == 200) {
            // if successful, decode the response body and store in cache for 30 seconds
            $body = $response->getBody()->getContents();
            $weatherData = json_decode($body);
            Cache::put($zipcode, $weatherData, 30);
        } else {
            // if error, set the weather data to "invalid location" for both variables
            $weatherData = (object) [
                'temperature' => 'invalid location',
                'conditions' => 'invalid location'
            ];
        }
    } catch (RequestException $e) {
        // if Guzzle throws an exception, set the weather data to appropriate error messages
        if ($e->hasResponse()) {
            $response = $e->getResponse();
            $statusCode = $response->getStatusCode();
            if ($statusCode == 400) {
                $weatherData = (object) [
                    'temperature' => 'invalid location',
                    'conditions' => 'invalid location'
                ];
	    } else {
		echo "<script>alert('{{$response->getBody()->getContents()}}');</script>";
                $weatherData = (object) [
                    'temperature' => 'Server Error',
                    'conditions' => 'Server Error'
                ];
            }
        } else {
            $weatherData = (object) [
                'temperature' => 'Error',
                'conditions' => 'Error'
            ];
        }
    }
}
$temp = $weatherData->temperature;
$conds = $weatherData->conditions;
?>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>
	<!-- JQuery -->
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

	<!-- Bootstrap -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
	<style>
.center{margin: auto;  
width: 50%;
  padding: 0 10px;
  text-align:center;
justify-content:center;}
a:link    { color: blue; }
a:visited { color: blue; }
.footer {
    background-color: #333;
    color: #fff;
    padding: 20px;
    text-align: center;
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
}
</style>
    </head>
<body>
<div class="center">
<a href={{url('/')}}>Home</a>
<a href={{url('/logout')}}>Logout</a>
{{$slot}}
</div>
<div class="footer">
<p>{{cas()->user()}}</p>
<p>Temperature: {{$temp}}</p>
<p>Conditions: {{$conds}}</p>
</div>
</body>
</html>

