<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use GuzzleHttp\Client;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware(['api.token'])->group(function () {
	Route::get('/author', function (Request $request) {
	    $name = $request->input('name');
	    if (!$name) {
	        return response()->json(['error' => 'Missing argument: name'], 400);
	    }
	    try {
	        $client = new Client();
	        $response = $client->request('GET', 'https://openlibrary.org/search/authors.json?q=' . urlencode($name));
	        $body = $response->getBody();
	        return response($body);
	    } catch (\Exception $e) {
	        // Handle any exceptions thrown during the request
	        return response()->json(['error' => $e->getMessage()]);
	    }
	});
});
