<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NumbersController extends Controller
{
	//
    public function index()
    {
        return view('numbers');
    }

    public function fact(Request $request)
    {
        $request->validate([
            'number' => 'required|numeric'
        ]);

        $number = $request->input('number');

        try {
        	$response = Http::get("http://numbersapi.com/$number");
        	if ($response->status() == 200) {
            		$fact = $response->body();
            		return view('numbers', compact('fact'));
        	} else {
        	    	$errorMessage = "An error occured while getting the facts";
            		return view('numbers', compact('errorMessage'));
        	}


    	} catch (Exception $e) {
        	$errorMessage = "An error occurred while fetching the fact. Please try again later.";

        	return view('numbers', compact('errorMessage'));
    	}
    }
}
