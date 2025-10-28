<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use \App\Http\Controllers\HalamanController;

/* Route::get("/welcome", function(){
    // return "Selamat Datang di Laravel";
    return view("welcome");
}); */
Route::get("/welcome/{nama}", [HalamanController::class, "welcome"]);

/* Route::get("/about", function(){
    // return "Ini adalah halaman about";
    return view("about");
}); */
Route::get("/about", [HalamanController::class, "about"]);

/* Route::get("/contact", function(){
    // return "Ini adalah halaman contact";
    return view("contact");
}); */
Route::get("/contact", [HalamanController::class, "contact"]);
