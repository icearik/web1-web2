<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\NumbersController;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

/*
 * Ilyar Aisarov Final Project
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware(['cas.auth'])->group(function() {

  Route::get('/', function () {
      return view('home');
  });
  Route::get('/profile', function () {
      return view('profile');
  });
  Route::post('/profile', function (Request $request) {
	$request->validate([
	   'zipcode' => 'required|numeric|digits:5'
	]);
	$zipcode = $request->input('zipcode');
	session(['zipcode' => $zipcode]);
	return redirect('/');
  });
  Route::get('/numbers', [NumbersController::class, 'index']);
  Route::post('/numbers', [NumbersController::class, 'fact']);
  Route::get('/authors', function (Request $request) {
  	return view('authors');
  });
  Route::get('/authors/{key}', function (Request $request, string $key) {
	return view('books', ['authorKey' => $key]);
  });
  Route::get('/api', function () {
	return view('birds');
  });
  Route::get('/auth', function () {
	$authKey = Str::random(40);
	Cache::forever('authKey', $authKey);
	session(['authKey' => $authKey]);
	$client = new Client();
    	$secretValue = env('AWS_SECRET');
    	try {
            $response = $client->post('https://bzyot6o52h.execute-api.us-east-1.amazonaws.com/prod/token', [
              'headers' => [
                	'AWS-Secret' => $secretValue,
                 	'Content-Type' => 'application/json',
              ],
              'body' => json_encode(['token' => $authKey])
            ]);

            return response()->json(['message' => 'OK']);
    	} catch (RequestException $e) {
            if ($e->hasResponse()) {
              $response = $e->getResponse();
              $message = $response->getBody();
            } else {
              $message = $e->getMessage();
            }
            return response()->json(['message' => 'Error: ' . $message], 500);
        }
  });
  Route::get('/logout', function () {
	  session()->invalidate();
	  Cache::flush();
	  cas()->logout();
        return redirect('/');
  });
});
