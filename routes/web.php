<?php
use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\AutoCompleteController;
use App\Models\Movie;
use App\Http\Controllers\SearchController;

Route::get('/', function () {
   $movies = Movie::limit(20)->get(); // Retrieve only 20 movies
   return view('welcome', [
       'movies' => $movies
   ]);
});

Route::get('/search/{search}', [SearchController::class, 'search']);

Route::get('/autocomplete/{param}', [SearchController::class, 'autocomplete']);