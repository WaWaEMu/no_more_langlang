<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/password-reset/{token}', function (Request $request, $token) {
    session([
        'token' => $token,
        'email' => $request->query('email'),
    ]);

    return redirect((config('app.frontend_url') . '/#/auth/reset'));
});

Route::middleware('web')->get('/api/password-reset/session', function () {
    return response()->json([
        'token' => session('token'),
        'email' => session('email'),
    ]);
});

require __DIR__.'/auth.php';
